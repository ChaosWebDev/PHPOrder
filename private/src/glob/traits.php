<?php
trait database
{
    public function getConnection()
    {
        $dsn = "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $conn = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], $options);
        return $conn;
    }

    public function query($sql)
    {
        $conn = $this->getConnection();
        return $conn->query($sql)->fetch(PDO::FETCH_ASSOC);
    }

    public function execute($sql, $params)
    {
        $conn = $this->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

trait fileHandler
{
    public function getFileContents($url)
    {
        return file_get_contents($url);
    }

    public function getJsonDecodedFileContents($url, $bool = false)
    {
        $file = $this->getFileContents($url);
        return json_decode($file, $bool);
    }

    public function setFileContents($url, $content, $options = null)
    {
        return file_put_contents($url, $content, $options);
    }
}
