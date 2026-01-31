<?php

namespace App\Services;

use Carbon\Carbon;
use Exception;
use Generator;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;

class OaiPmhService
{
    /**
     * Fetch records from the OAI-PMH endpoint.
     *
     * @param  string  $url  The OAI-PMH base URL
     * @param  string|null  $setSpec  Optional set specification to filter by
     * @param  Carbon|null  $from  Optional date to fetch records from
     * @return Generator<int, array{
     *     oai_id: string,
     *     title: string,
     *     authors: array<int, string>,
     *     abstract: string,
     *     keywords: string,
     *     published_date: string,
     *     url: string|null,
     *     doi: string|null,
     *     publisher: string,
     *     year: string|null,
     *     pages: string|null,
     *     issue_title: string|null,
     *     issue_title: string|null,
     *     journal_title: string|null,
     *     pdf_url: string|null
     * }>
     *
     * @throws Exception
     */
    public function listRecords(string $url, ?string $setSpec = null, ?Carbon $from = null): Generator
    {
        $verb = 'ListRecords';
        $metadataPrefix = 'oai_dc';
        /** @var string|null $resumptionToken */
        $resumptionToken = null;

        do {
            /** @var array<string, string> $params */
            $params = [];
            if ($resumptionToken) {
                $params['verb'] = $verb;
                $params['resumptionToken'] = $resumptionToken;
            } else {
                $params = [
                    'verb' => $verb,
                    'metadataPrefix' => $metadataPrefix,
                ];
                if ($setSpec) {
                    $params['set'] = $setSpec;
                }
                if ($from) {
                    $params['from'] = $from->format('Y-m-d');
                }
            }

            try {
                /** @var Response $response */
                $response = Http::timeout(120)
                    ->retry(3, 100) // Retry 3 times with 100ms delay
                    ->get($url, $params);

                if ($response->failed()) {
                    Log::error('OAI-PMH Request Failed: '.$url, ['status' => $response->status(), 'body' => $response->body()]);
                    throw new Exception('OAI-PMH Request Failed: '.$response->status());
                }

                $xmlString = $response->body();

                /** @var SimpleXMLElement|false $xml */
                $xml = simplexml_load_string($xmlString);
                if ($xml === false) {
                    Log::error('Failed to parse XML from '.$url);
                    throw new Exception('Failed to parse XML');
                }

                // Check for OAI errors
                /** @phpstan-ignore-next-line */
                if (isset($xml->error)) {
                    /** @phpstan-ignore-next-line */
                    $errorCode = (string) $xml->error['code'];
                    if ($errorCode === 'noRecordsMatch') {
                        return; // No new records
                    }
                    /** @phpstan-ignore-next-line */
                    throw new Exception('OAI Error: '.$xml->error);
                }

                /** @phpstan-ignore-next-line */
                if (! isset($xml->ListRecords->record)) {
                    return;
                }

                /** @phpstan-ignore-next-line */
                foreach ($xml->ListRecords->record as $record) {
                    /** @var SimpleXMLElement $record */
                    /** @phpstan-ignore-next-line */
                    if (isset($record->header['status']) && (string) $record->header['status'] === 'deleted') {
                        continue; // Skip deleted records for now
                    }

                    yield $this->parseRecord($record);
                }

                // Check for resumptionToken
                $resumptionToken = null;
                /** @phpstan-ignore-next-line */
                if (isset($xml->ListRecords->resumptionToken) && (string) $xml->ListRecords->resumptionToken !== '') {
                    /** @phpstan-ignore-next-line */
                    $resumptionToken = (string) $xml->ListRecords->resumptionToken;
                }
            } catch (Exception $e) {
                Log::error('OAI-PMH Error: '.$e->getMessage());
                throw $e;
            }
        } while ($resumptionToken);
    }

    /**
     * Parse a single OAI-PMH record into an array.
     *
     * @param  SimpleXMLElement  $record  The XML record element
     * @return array{
     *     oai_id: string,
     *     title: string,
     *     authors: array<int, string>,
     *     abstract: string,
     *     keywords: string,
     *     published_date: string,
     *     url: string|null,
     *     doi: string|null,
     *     publisher: string,
     *     year: string|null,
     *     pages: string|null,
     *     issue_title: string|null,
     *     issue_title: string|null,
     *     journal_title: string|null,
     *     pdf_url: string|null
     * }
     */
    protected function parseRecord(SimpleXMLElement $record): array
    {
        /** @phpstan-ignore-next-line */
        $header = $record->header;
        /** @phpstan-ignore-next-line */
        $metadata = $record->metadata->children('oai_dc', true)->dc->children('dc', true);

        /** @phpstan-ignore-next-line */
        $oaiId = (string) $header->identifier;
        /** @phpstan-ignore-next-line */
        $title = (string) $metadata->title;

        /** @var array<int, string> $creators */
        $creators = [];
        /** @phpstan-ignore-next-line */
        foreach ($metadata->creator as $creator) {
            $creators[] = $this->formatName((string) $creator);
        }

        /** @phpstan-ignore-next-line */
        $description = (string) $metadata->description;

        /** @var array<int, string> $subjects */
        $subjects = [];
        /** @phpstan-ignore-next-line */
        foreach ($metadata->subject as $currSubject) {
            $subjects[] = (string) $currSubject;
        }
        $subject = implode(', ', $subjects);

        /** @phpstan-ignore-next-line */
        $date = (string) $metadata->date;
        /** @phpstan-ignore-next-line */
        $type = (string) $metadata->type;
        /** @phpstan-ignore-next-line */
        $publisher = (string) $metadata->publisher;

        /** @phpstan-ignore-next-line */
        $source = (string) $metadata->source;
        $parsedSource = $this->parseSource($source);

        /** @var array<int, string> $identifiers */
        $identifiers = [];
        /** @phpstan-ignore-next-line */
        foreach ($metadata->identifier as $identifier) {
            $identifiers[] = (string) $identifier;
        }

        // Find URL (often in identifier)
        $url = null;
        $doi = null;
        foreach ($identifiers as $id) {
            if (str_starts_with($id, 'http')) {
                $url = $id;
            }
            if (str_contains($id, 'doi.org') || str_starts_with($id, '10.')) {
                $doi = $id;
            }
        }

        // Find PDF URL in relation
        $pdfUrl = null;
        /** @phpstan-ignore-next-line */
        if (isset($metadata->relation)) {
            foreach ($metadata->relation as $relation) {
                $rel = (string) $relation;
                // Basic heuristic: check if it looks like a file link or contains 'download'/'view' + 'pdf'
                if (str_ends_with(strtolower($rel), '.pdf') ||
                    (str_contains($rel, '/view/') && str_contains(strtolower($rel), 'pdf')) ||
                    (str_contains($rel, '/download/') && str_contains(strtolower($rel), 'pdf'))) {
                    $pdfUrl = $rel;
                    break;
                }

                // Fallback: if we haven't found a PDF but this is a relation link that is different from the main URL, maybe check it?
                // But for now, let's stick to the user's specific hint "relation".
                // Many OJS installations put the PDF galley link in dc:relation.
                if (! $pdfUrl && str_contains($rel, '/view/')) {
                    // Potential PDF view page
                    $pdfUrl = $rel;
                }
            }
        }

        return [
            'oai_id' => $oaiId,
            'title' => $title,
            'authors' => $creators,
            'abstract' => $description,
            'keywords' => $subject,
            'published_date' => $date,
            'url' => $url,
            'pdf_url' => $pdfUrl,
            'doi' => $doi,
            'publisher' => $publisher,
            'year' => $parsedSource['year'] ?? null,
            'pages' => $parsedSource['pages'] ?? null,
            'issue_title' => $parsedSource['issue_title'] ?? null,
            'journal_title' => $parsedSource['journal_title'] ?? null,
        ];
    }

    /**
     * Format name from "Lastname, Firstname" to "Firstname Lastname".
     */
    protected function formatName(string $name): string
    {
        if (str_contains($name, ',')) {
            $parts = explode(',', $name);
            if (count($parts) >= 2) {
                return trim($parts[1]).' '.trim($parts[0]);
            }
        }

        return trim($name);
    }

    /**
     * Parse source string for year, pages, etc.
     *
     * @param  string  $source  The source string from OAI metadata
     * @return array{year: string|null, pages: string|null, issue_title: string|null, journal_title: string|null}
     *
     * @example "Fibonacci: Jurnal Ilmu Ekonomi; Vol. 1 No. 1 (2024); 1-7"
     */
    protected function parseSource(string $source): array
    {
        $result = [
            'year' => null,
            'pages' => null,
            'issue_title' => null,
            'journal_title' => null,
        ];

        // Extract Year: (2024)
        if (preg_match('/\((\d{4})\)/', $source, $matches)) {
            $result['year'] = $matches[1];
        }

        // Extract Pages: ; 1-7 at the end
        if (preg_match('/;\s*(\d+-\d+)\s*$/', $source, $matches)) {
            $result['pages'] = $matches[1];
        }

        // Extract Issue Title (simplified attempt)
        // This is tricky as formats vary widely.
        // We might take "Vol. X No. Y" if present.
        if (preg_match('/(Vol\.?\s*\d+\s*No\.?\s*\d+)/i', $source, $matches)) {
            $result['issue_title'] = $matches[1];
        }

        return $result;
    }
}
