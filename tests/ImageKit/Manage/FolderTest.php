<?php

namespace ImageKit\Tests\ImageKit\Manage;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use ImageKit\ImageKit;
use ImageKit\Manage\Folder;
use ImageKit\Resource\GuzzleHttpWrapper;
use PHPUnit\Framework\TestCase;

class FolderTest extends TestCase
{
    /**
     * @var ImageKit
     */
    private $client;

    protected function setUp(): void
    {
        $this->client = new ImageKit(
            'Testing_Public_Key',
            'Testing_Private_Key',
            'https://ik.imagekit.io/demo'
        );
    }

    protected function tearDown(): void
    {
        $this->client = null;
    }

    private function stubHttpClient($methodName, $response)
    {
        $stub = $this->createMock(GuzzleHttpWrapper::class);
        $stub->method('setDatas');
        $stub->method($methodName)->willReturn($response);

        $closure = function () use ($stub) {
            $this->httpClient = $stub;
        };
        $doClosure = $closure->bindTo($this->client, ImageKit::class);
        $doClosure();
    }

    public function testCreateInvalidFolderName()
    {
        $folderName = '';
        $parentFolderPath = '/';

        $mockBodyResponse = Utils::streamFor();

        $this->stubHttpClient('post', new Response(201, ['X-Foo' => 'Bar'], $mockBodyResponse));

        $response = $this->client->createFolder($folderName, $parentFolderPath);

        FolderTest::assertEquals('Missing data for creation of folder', $response->err->message);
    }

    public function testCreateInvalidParentFolderPath()
    {
        $folderName = '/testing';
        $parentFolderPath = '';

        $mockBodyResponse = Utils::streamFor();

        $this->stubHttpClient('post', new Response(201, ['X-Foo' => 'Bar'], $mockBodyResponse));

        $response = $this->client->createFolder($folderName, $parentFolderPath);

        FolderTest::assertEquals('Missing data for creation of folder', $response->err->message);
    }

    public function testCreate()
    {
        $folderName = 'testing';
        $parentFolderPath = '/';

        $mockBodyResponse = Utils::streamFor();

        $this->stubHttpClient('post', new Response(201, ['X-Foo' => 'Bar'], $mockBodyResponse));

        $response = $this->client->createFolder($folderName, $parentFolderPath);

        FolderTest::assertNull($response->success);
        FolderTest::assertNull($response->err);
    }

    public function testDeleteEmptyFolderPath()
    {
        $folderPath = '';

        $mockBodyResponse = Utils::streamFor();

        $this->stubHttpClient('delete', new Response(204, ['X-Foo' => 'Bar'], $mockBodyResponse));

        $response = $this->client->deleteFolder($folderPath);

        FolderTest::assertEquals('Missing data for deletion of folder', $response->err->message);
    }

    public function testDelete()
    {
        $folderPath = '/testing';

        $mockBodyResponse = Utils::streamFor();

        $this->stubHttpClient('delete', new Response(204, ['X-Foo' => 'Bar'], $mockBodyResponse));

        $response = $this->client->deleteFolder($folderPath);

        FolderTest::assertNull($response->success);
        FolderTest::assertNull($response->err);
    }

    public function testCopyEmptySource()
    {
        $sourceFolderPath = '';
        $destinationPath = '/testing2';

        $mockBodyResponse = Utils::streamFor(json_encode([ 'jobId' => 'Testing_job_id' ]));

        $this->stubHttpClient('post', new Response(200, ['X-Foo' => 'Bar'], $mockBodyResponse));

        $response = $this->client->copyFolder($sourceFolderPath, $destinationPath);

        FolderTest::assertEquals('Missing data for copying folder', $response->err->message);
    }

    public function testCopyEmptyDestinationPath()
    {
        $sourceFolderPath = '/';
        $destinationPath = '';

        $mockBodyResponse = Utils::streamFor(json_encode([ 'jobId' => 'Testing_job_id' ]));

        $this->stubHttpClient('post', new Response(200, ['X-Foo' => 'Bar'], $mockBodyResponse));

        $response = $this->client->copyFolder($sourceFolderPath, $destinationPath);

        FolderTest::assertEquals('Missing data for copying folder', $response->err->message);
    }

    public function testCopy()
    {
        $sourceFolderPath = '/testing';
        $destinationPath = '/testing2';

        $mockBodyResponse = Utils::streamFor(json_encode([ 'jobId' => 'Testing_job_id' ]));

        $this->stubHttpClient('post', new Response(200, ['X-Foo' => 'Bar'], $mockBodyResponse));

        $response = $this->client->copyFolder($sourceFolderPath, $destinationPath);

        $el = get_object_vars($response->success);
        FolderTest::assertEquals('Testing_job_id', $el['jobId']);
    }

    public function testMoveEmptySource()
    {
        $sourceFolderPath = '';
        $destinationPath = '/testing2';

        $mockBodyResponse = Utils::streamFor(json_encode([ 'jobId' => 'Testing_job_id' ]));

        $this->stubHttpClient('post', new Response(200, ['X-Foo' => 'Bar'], $mockBodyResponse));

        $response = $this->client->moveFolder($sourceFolderPath, $destinationPath);

        FolderTest::assertEquals('Missing data for moving folder', $response->err->message);
    }

    public function testMoveEmptyDestinationPath()
    {
        $sourceFolderPath = '/';
        $destinationPath = '';

        $mockBodyResponse = Utils::streamFor(json_encode([ 'jobId' => 'Testing_job_id' ]));

        $this->stubHttpClient('post', new Response(200, ['X-Foo' => 'Bar'], $mockBodyResponse));

        $response = $this->client->moveFolder($sourceFolderPath, $destinationPath);

        FolderTest::assertEquals('Missing data for moving folder', $response->err->message);
    }

    public function testMove()
    {
        $sourceFolderPath = '/testing';
        $destinationPath = '/testing2';

        $mockBodyResponse = Utils::streamFor(json_encode([ 'jobId' => 'Testing_job_id' ]));

        $this->stubHttpClient('post', new Response(200, ['X-Foo' => 'Bar'], $mockBodyResponse));

        $response = $this->client->moveFolder($sourceFolderPath, $destinationPath);

        $el = get_object_vars($response->success);
        FolderTest::assertEquals('Testing_job_id', $el['jobId']);
    }
}
