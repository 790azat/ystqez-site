<?php

namespace Tests\Unit;

use App\Models\InstagramPost;
use App\Models\Video;
use App\Services\ContentImporter;
use App\Services\YouTubeService;
use App\Support\Settings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_json_importer_is_defensive_and_idempotent(): void
    {
        $dir = sys_get_temp_dir().'/yq-import-'.uniqid();
        mkdir($dir);
        file_put_contents($dir.'/videos.json', json_encode([
            ['id' => 'dQw4w9WgXcQ', 'title' => 'Первый', 'upload_date' => '20240115', 'duration' => 3600, 'view_count' => 100, 'type' => 'video', 'tags' => ['дружба']],
            ['id' => 'abcdefghijk', 'type' => 'short'],
            ['title' => 'без id'],
            'garbage',
        ]));
        file_put_contents($dir.'/channel.json', json_encode(['title' => 'Yst Qez', 'subscriber_count' => 1500]));
        file_put_contents($dir.'/instagram.json', json_encode(['profile' => ['followers' => 10], 'posts' => [
            ['shortcode' => 'XYZ', 'caption' => 'Кадр', 'date' => '2024-05-01T10:00:00', 'media_urls' => ['https://x/y.jpg'], 'likes' => 5, 'local_image' => 'media/instagram/XYZ.jpg'],
            ['caption' => 'нет shortcode'],
        ]]));

        $importer = new ContentImporter($dir);
        $this->assertSame(['videos' => 2, 'channel' => 'ok', 'instagram' => 1], $importer->run());
        $importer->run();

        $this->assertSame(2, Video::count());
        $v = Video::where('youtube_id', 'dQw4w9WgXcQ')->first();
        $this->assertSame('2024-01-15', $v->published_at->toDateString());
        $this->assertSame(['дружба'], $v->tags);
        $this->assertSame('1500', Settings::get('channel_subscribers'));
        $this->assertSame(1, InstagramPost::count());
        $this->assertStringEndsWith('/media/instagram/XYZ.jpg', InstagramPost::first()->image_url);
        array_map('unlink', glob($dir.'/*'));
        rmdir($dir);
    }

    public function test_media_url_helper(): void
    {
        config(['media.cdn_url' => 'https://cdn.example.com/repo@main/public']);
        $this->assertSame('https://cdn.example.com/repo@main/public/media/a.jpg', media_url('media/a.jpg'));
        $this->assertSame('https://cdn.example.com/repo@main/public/media/a.jpg', media_url('/a.jpg'));
        $this->assertSame('https://ext.com/x.png', media_url('https://ext.com/x.png'));
        $this->assertNull(media_url(''));
        config(['media.cdn_url' => '']);
        $this->assertSame(url('media/a.jpg'), media_url('media/a.jpg'));
    }

    public function test_rss_import(): void
    {
        $xml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns:yt="http://www.youtube.com/xml/schemas/2015" xmlns:media="http://search.yahoo.com/mrss/" xmlns="http://www.w3.org/2005/Atom">
 <entry>
  <id>yt:video:dQw4w9WgXcQ</id>
  <yt:videoId>dQw4w9WgXcQ</yt:videoId>
  <title>Новый выпуск</title>
  <link rel="alternate" href="https://www.youtube.com/watch?v=dQw4w9WgXcQ"/>
  <published>2024-03-01T12:00:00+00:00</published>
  <media:group>
   <media:title>Новый выпуск</media:title>
   <media:thumbnail url="https://i1.ytimg.com/vi/dQw4w9WgXcQ/hqdefault.jpg" width="480" height="360"/>
   <media:description>Описание 01:30 тема</media:description>
   <media:community><media:starRating count="42" average="5.00" min="1" max="5"/><media:statistics views="1234"/></media:community>
  </media:group>
 </entry>
 <entry>
  <yt:videoId>abcdefghijk</yt:videoId>
  <title>Шорт</title>
  <link rel="alternate" href="https://www.youtube.com/shorts/abcdefghijk"/>
  <published>2024-03-02T12:00:00+00:00</published>
 </entry>
</feed>
XML;
        $r = (new YouTubeService)->importRss($xml);
        $this->assertSame(['created' => 2, 'updated' => 0], $r);
        $v = Video::where('youtube_id', 'dQw4w9WgXcQ')->first();
        $this->assertSame(1234, $v->view_count);
        $this->assertSame(42, $v->like_count);
        $this->assertSame('Описание 01:30 тема', $v->description);
        $this->assertStringContainsString('data-seek="90"', $v->description_html);
        $this->assertSame('short', Video::where('youtube_id', 'abcdefghijk')->value('type'));

        $this->assertSame(['created' => 0, 'updated' => 2], (new YouTubeService)->importRss($xml));
    }

    public function test_extract_youtube_id(): void
    {
        foreach (['https://www.youtube.com/watch?v=dQw4w9WgXcQ&t=1', 'https://youtu.be/dQw4w9WgXcQ', 'https://www.youtube.com/shorts/dQw4w9WgXcQ', 'dQw4w9WgXcQ'] as $u) {
            $this->assertSame('dQw4w9WgXcQ', Video::extractYoutubeId($u));
        }
        $this->assertNull(Video::extractYoutubeId('https://example.com'));
    }
}
