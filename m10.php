<?php
//constant($root = "C:\\xampp\htdocs\welcome");

class TelegraphText
{
    public $title;
    public $text;
    public $author;
    public $published;
    public $slug;
    public $fileStorage;

    public function __construct($author, $slug, $fileStorage)
    {
        $this->author =  $author;
        $this->slug = $slug;
        $this->published = date("d/m/y/h/i");
        $this->fileStorage = $fileStorage;
    }

    public function storeText()
    {
        $arrayText = [
            'author' => $this->author,
            'title' => $this->title,
            'text' => $this->text,
            'published' => $this->published,
        ];
        file_put_contents($this->slug, serialize($arrayText));
    }

    public function loadText()
    {
        if (file_exists($this->slug)) {
            $arrayText = unserialize(file_get_contents($this->slug));
            $this->author = $arrayText['author'];
            $this->title = $arrayText['title'];
            $this->text = $arrayText['text'];
            $this->published = $arrayText['published'];
            var_dump($this->text);
        }
    }

    public function editText($title, $text)
    {
        $this->title = $title;
        $this->text = $text;
    }
}

abstract class Storage implements EventListenerInterface, LoggerInterface
{
    abstract public function create($telegraphText);
    abstract public function read($slug);
    abstract public function update($slug, $telegraphText);
    abstract public function delete($slug);
    abstract public function list();
}

abstract class View
{
    public function __construct($storage)
    {
    }
    abstract public function displayTextById();
    abstract public function displayTextByUrl();
}

abstract class User implements EventListenerInterface
{
    public $id;
    public $name;
    public $role;

    abstract public function getTextToEdit();
}

class FileStorage extends Storage
{
    const STORAGE_DIR = 'storage' . DIRECTORY_SEPARATOR;

    public function create($telegraphText): string
    {
        $telegraphText->slug = $telegraphText->slug . '_' . date("d/m/y/h/i");

        $i = 1;
        while (file_exists(self::STORAGE_DIR . $telegraphText->slug . '_' . $i)) {
            $i++;
        }

        $telegraphText->slug .= '_' .$i;

        file_put_contents(self::STORAGE_DIR . $telegraphText->slug ,serialize($telegraphText));

        return $telegraphText->slug;
    }

    public function read($slug)
    {
        if (file_exists($slug)) {
            return( unserialize(file_get_contents($slug)));
        }
        return null;
    }

    public function update($slug, $telegraphText)
    {
        file_put_contents(self::STORAGE_DIR . $slug, serialize($telegraphText));
    }

    public function delete($slug)
    {
        if (file_exists($slug)) {
            unlink("C:\\xampp\htdocs\welcome". DIRECTORY_SEPARATOR .$slug);
        }
        return 'Файл для удаления не найден';
    }

    public function list()
    {
        $telegraphTexts = [];
        $arrayFiles = scandir("C:\\xampp\htdocs\welcome\storage");
        foreach ($arrayFiles as $value) {
            if ($value != '.' && $value != '..') {
                $telegraphTexts[] = unserialize(file_get_contents($value));
            }
        }
        return $telegraphTexts;
    }

    function attachEvent($methodName)
    {
    }

    function detouchEvent($methodName)
    {
    }

    function logMessage($erorText)
    {
    }

    function lastMessages($countErorMassage)
    {
    }
}

interface LoggerInterface
{
    public function logMessage($erorText);
    public function lastMessages($countErorMassage);
}

interface EventListenerInterface
{
    public function attachEvent($methodName);
    public function detouchEvent($methodName);
}
