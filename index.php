<?php

abstract class Storage {
    abstract public function create($object, $slug);
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

    public function create($object, $slug)
    {
        $date = date('Y-m-d');
        $filename = $slug . '_' . $date;
        $i = 1;
        while (file_exists(__DIR__ . '/serializedFiles/' . $filename)) {
            $filename = $slug . '_' . $date . '_' . $i ;
            $i++;
        }
        var_dump($serializedData = serialize($object));
        file_put_contents(__DIR__ . '/serializedFiles/' . $filename, $serializedData);
        return $filename;
    }

    public function read($slug)
    {
        $filename = __DIR__ . '/serializedFiles/' . $slug;
        if (!file_exists($filename)) {
            return null;
        }
        $serializedData = file_get_contents($filename);
        $data = unserialize($serializedData);
        return $serializedData;
    }

    public function update($idOrSlug, $object) 
    {
        $data = serialize($object) . 'updated';
        file_put_contents(__DIR__ . '/serializedFiles/' . $idOrSlug, $data);
        return $data;
    }
    

    public function delete($slug)
    {
        $filename = __DIR__ . '/serializedFiles/' . $slug;
        if (!file_exists($filename)) {
            return false;
        }
        unlink($filename);
        return true;
    }

    public function list()
    {
        
        $files = array_diff(scandir(__DIR__ . '/serializedFiles/'), ['..', '.']);
        $objArr = [];
        
        foreach($files as $file) {   
      
            $objArr = unserialize(file_get_contents(__DIR__ . '/serializedFiles/' . $file)); 
        }
            return $objArr;
        
    }
}
include 'TelegraphText.php';

$fileStorageObj = new FileStorage();
$slug = '/serializedFiles/serialezed_2023-05-08.txt';


$slug = $fileStorageObj -> create($telegraphText, 'serialezed');
$fileStorageObj -> read($slug);
$fileStorageObj -> update($slug, $telegraphText);
$fileStorageObj -> delete($slug);
$fileStorageObj -> list();

