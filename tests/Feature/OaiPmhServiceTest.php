<?php

namespace Tests\Feature;

use App\Services\OaiPmhService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OaiPmhServiceTest extends TestCase
{
    public function test_it_parses_record_correctly_with_keywords_and_pdf_url()
    {
        $xml = <<<XML
<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd">
  <responseDate>2024-01-31T00:00:00Z</responseDate>
  <request verb="ListRecords" metadataPrefix="oai_dc">http://example.com/oai</request>
  <ListRecords>
    <record>
      <header>
        <identifier>oai:example.com:123</identifier>
        <datestamp>2024-01-01</datestamp>
      </header>
      <metadata>
        <oai_dc:dc xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd">
          <dc:title>Test Title</dc:title>
          <dc:creator>Smith, John</dc:creator>
          <dc:creator>Doe, Jane</dc:creator>
          <dc:subject>Keyword One</dc:subject>
          <dc:subject>Keyword Two</dc:subject>
          <dc:identifier>http://example.com/view/123</dc:identifier>
          <dc:relation>http://example.com/download/123.pdf</dc:relation>
        </oai_dc:dc>
      </metadata>
    </record>
  </ListRecords>
</OAI-PMH>
XML;

        Http::fake([
            'example.com/oai*' => Http::response($xml, 200),
        ]);

        $service = new OaiPmhService();
        $records = $service->listRecords('http://example.com/oai');
        $data = [];
        foreach ($records as $record) {
            $data[] = $record;
        }

        $this->assertCount(1, $data);
        $record = $data[0];

        $this->assertEquals('Test Title', $record['title']);
        $this->assertEquals(['John Smith', 'Jane Doe'], $record['authors']); // Check name formatting too
        // Check keywords joined
        $this->assertEquals('Keyword One, Keyword Two', $record['keywords']);
        // Check PDF URL
        $this->assertEquals('http://example.com/download/123.pdf', $record['pdf_url']);
        // Check Source URL
        $this->assertEquals('http://example.com/view/123', $record['url']);
    }
}
