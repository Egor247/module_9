<?php

abstract class Storage {
    abstract public function create($object);
    abstract public function read($id_or_slug);
    abstract public function update($id_or_slug, $updated_object);
    abstract public function delete($id_or_slug);
    abstract public function list();
}


abstract class View {
    protected $storage;
    
    public function __construct(Storage $storage) {
        $this->storage = $storage;
    }
    
    abstract public function displayTextById($id);
    abstract public function displayTextByUrl($url);
}


abstract class User {
    protected $id;
    protected $name;
    protected $role;
    
    public function __construct($id, $name, $role) {
        $this->id = $id;
        $this->name = $name;
        $this->role = $role;
    }
    
    abstract public function getTextsToEdit();
}

class FileStorage extends Storage
{

    public function create($object)
    {
        $serializedData = serialize($object);
        $date = date('Y-m-d');
        $slug = md5($serializedData) . '_' . $date;
        $i = 1;
        while (file_exists(__DIR__ . '/serializedFiles/' . $slug)) {
            $slug = md5($serializedData) . '_' . $date . '_' . $i ;
            $i++;
        }
        file_put_contents(__DIR__ . '/serializedFiles/' . $slug, $serializedData);
        return $slug;
    }

    public function read($slug)
    {
        $filename = __DIR__ . '/serializedFiles/' . $slug;
        if (!file_exists($filename)) {
            return null;
        }
        $serializedData = file_get_contents($filename);
        $data = unserialize($serializedData);
        return $data;
    }

    public function update($idOrSlug, $object) 
    {
        $data = serialize($object) . 'updated';
        file_put_contents(__DIR__ . '/serializedFiles/' . $idOrSlug, $data);
         var_dump($data);
    }
    

    public function delete($slug)
    {
        $filename = __DIR__ . '/serializedFiles/' . $slug;
        if (!file_exists($filename)) {
            return false;
        }
        unlink($filename);
        
    }

    public function list()
    {
        
        $files = array_diff(scandir(__DIR__ . '/serializedFiles/'), ['..', '.']);
        $objArr = [];
        
        foreach($files as $file) {   
      
            $objArr[] = unserialize(file_get_contents(__DIR__ . '/serializedFiles/' . $file)); 
        }
            return $objArr;
        
    }
}
include 'TelegraphText.php';

$fileStorageObj = new FileStorage();
$slug = '/serializedFiles/serialezed_2023-05-08.txt';


var_dump($slug = $fileStorageObj -> create($telegraphText));
echo '<hr> read';
var_dump($fileStorageObj -> read($slug));
echo '<hr> update';
var_dump($fileStorageObj -> update($slug, $telegraphText));
$fileStorageObj -> delete($slug);
echo '<hr> list';
var_dump($fileStorageObj -> list());

