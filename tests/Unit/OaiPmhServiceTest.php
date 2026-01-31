<?php

use App\Services\OaiPmhService;
use Illuminate\Support\Facades\Http;

uses(\Tests\TestCase::class);

test('formatName converts Lastname, Firstname to Firstname Lastname', function () {
    $service = new OaiPmhService();
    $method = new ReflectionMethod(OaiPmhService::class, 'formatName');
    $method->setAccessible(true);

    expect($method->invoke($service, 'Satriawan, Nofri'))->toBe('Nofri Satriawan');
    expect($method->invoke($service, 'Doe, John'))->toBe('John Doe');
    expect($method->invoke($service, 'SingleName'))->toBe('SingleName');
});

test('parseSource extracts year and pages correctly', function () {
    $service = new OaiPmhService();
    $method = new ReflectionMethod(OaiPmhService::class, 'parseSource');
    $method->setAccessible(true);

    $source = 'Fibonacci: Jurnal Ilmu Ekonomi, Manajemen dan Keuangan; Vol. 1 No. 1 (2024): Fibonacci: Jurnal Ilmu Ekonomi, Manajemen, dan Keuangan ; 1-7';
    $result = $method->invoke($service, $source);

    expect($result['year'])->toBe('2024');
    expect($result['pages'])->toBe('1-7');
    // expect($result['issue_title'])->toContain('Vol. 1 No. 1');
});

test('listRecords yields parsed records from XML', function () {
    Http::fake([
        '*' => Http::response(<<<'XML'
<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <ListRecords>
        <record>
            <header>
                <identifier>oai:example.com:article/123</identifier>
            </header>
            <metadata>
                <oai_dc:dc xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/" xmlns:dc="http://purl.org/dc/elements/1.1/">
                    <dc:title>Test Article</dc:title>
                    <dc:creator>Satriawan, Nofri</dc:creator>
                    <dc:creator>Doe, Jane</dc:creator>
                    <dc:source>Journal Name; Vol. 2 No. 3 (2025): Issue Title ; 10-20</dc:source>
                </oai_dc:dc>
            </metadata>
        </record>
    </ListRecords>
</OAI-PMH>
XML
        , 200),
    ]);

    $service = new OaiPmhService();
    $records = iterator_to_array($service->listRecords('http://example.com/oai'));

    expect($records)->toHaveCount(1);
    expect($records[0]['title'])->toBe('Test Article');
    expect($records[0]['authors'])->toBe(['Nofri Satriawan', 'Jane Doe']);
    expect($records[0]['year'])->toBe('2025');
    expect($records[0]['pages'])->toBe('10-20');
});
