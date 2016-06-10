<?php
use Goutte\Client;
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config/config.php';

while (true) {
    foreach($story as $oneStory) {
        $spider = new Spider($config, $oneStory);
        $spider->start();
    }
    sleep(60);
}
class Spider {
    private $client ;
    private $connection ;
    private $story;
    private $begin_url ;
    private $config;
    public function __construct($config, $story) {
        $this->config = $config;
        $this->story = $story;
        $this->client = new Client();
        //数据库
        $this->connection = mysqli_connect($this->config['DB_HOST'], $this->config['DB_USER'], $this->config['DB_PWD'], $this->config['DB_NAME']);
        // Check connection
        if (mysqli_connect_errno($this->connection))
        {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }

        $this->connection->query("set names utf8");

        //手动从某一章开始
        $this->begin_url = $this->story['START_SECTION_URL'];
    
    }
    /**
     * 入口
     */
    public function start() {
        //获取需要开始爬取的url
        $query = "SELECT url FROM story WHERE story_name = '{$this->story['STORY_NAME']}' ORDER BY id DESC LIMIT 1";
        $res = $this->fetchOneRow($this->connection, $query);
        if($this->begin_url) {
            $url = $this->begin_url;
        
        } else if($res) {
            //最新一章
            $url = $res;
        }else {
            //第一章
            $url = $this->story['FIRST_SECTION_URL'];
        }
        $this->build($url);
        echo $this->story['STORY_NAME'] . "爬完了。";
    }

    /**
     * 获取数据，并保存
     */
    public function build($url) {
        //如果url不是html结尾，说明已经回到了列表，即爬完最新的章节了。
        $tail = substr($url, -4, 4);
        if( $tail != 'html' ) {
            return;
        }

        $crawler = $this->client->request('GET', $url);
        $title = $crawler->filter('title')->first()->text();//章节标题
        $content = $crawler->filter('#content')->first();
        if(empty($content)) {
            return;
        }
        $sectionContent = $content->html();//章节内容

        $query = "SELECT title FROM story WHERE url = '{$url}'";
        $thisSection = $this->fetchOneRow($this->connection, $query);

        if(!$thisSection) {
            //数据库未有此章节
            //保存
            $this->save($title, $sectionContent, $url, $this->story['STORY_NAME']);

        }
        try {
            //进入下一章
            $link = $crawler->selectLink("下一章")->link();
            $this->build($link->getUri());
        }catch (Exception $e) {
            echo $e->getMessage(); 
        }
    }

    public function save($title, $content, $url, $story_name) {
        $query = "INSERT INTO story (title, content, url, story_name, is_read, created_at) VALUES (?,?,?,?,?,?)";
        $stmt = $this->connection->prepare($query);
        $is_read = 1;
        $created_at = date('Y-m-d H:i:s');
        $stmt->bind_param("ssssss", $title, $content, $url, $story_name, $is_read,$created_at);
        $stmt->execute();
        $stmt->close();
    }
    public function fetchOneRow($con, $query) {
        $res = $con->query($query);
        $row = $res->fetch_row();
        if($row) {
            return $row[0];
        }else {
            return null;
        }
    }

}
