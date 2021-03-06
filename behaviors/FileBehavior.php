<?php
/**
 * FileBehavior class file.
 * @author Christoffer Niska <christoffer.niska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2013-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package crisu83.yii-filemanager.behaviors
 */

/**
 * File behavior for active records that are associated with files.
 */
class FileBehavior extends CActiveRecordBehavior
{
    /**
     * @var string the name of the model attribute that holds the file id (defaults to 'fileId').
     */
    public $idAttribute = 'fileId';
    /**
     * @var string the component id for the file manager component (defaults to 'fileManager').
     */
    public $componentID = 'fileManager';

    /** @var FileManager */
    private $_fileManager;

    /**
     * Saves the given file both in the database and on the hard disk.
     * @param CUploadedFile $file the uploaded file.
     * @param string $name the new name for the file.
     * @param string $path the path relative to the base path.
     * @return File the model.
     * @see FileManager::saveModel
     */
    public function saveFile($file, $name = null, $path = null)
    {
        $model = $this->getFileManager()->saveModel($file, $name, $path);
        if ($model === null) {
            return null;
        }
        $this->owner->{$this->idAttribute} = $model->id;
        $this->owner->save(true, array($this->idAttribute));
        return $model;
    }

    /**
     * Returns the file with the given id.
     * @return File the model.
     * @see FileManager::loadModel
     */
    public function loadFile()
    {
        $id = $this->owner->{$this->idAttribute};
        return $this->getFileManager()->loadModel($id);
    }

    /**
     * Deletes the file associated with the owner.
     * @return boolean whether the file was deleted.
     */
    public function deleteFile()
    {
        $id = $this->owner->{$this->idAttribute};
        return $this->getFileManager()->deleteModel($id);
    }

    /**
     * Returns the full path for the given model.
     * @param File $model the file model.
     * @return string the path.
     * @see FileManager::resolveFileUrl
     */
    public function resolveFileUrl()
    {
        $model = $this->loadFile();
        return $model->resolveUrl($model);
    }

    /**
     * Returns the full url for the given model.
     * @param File $model the file model.
     * @return string the url.
     * @see FileManager::resolveFilePath
     */
    public function resolveFilePath()
    {
        $model = $this->loadFile();
        return $model->resolvePath($model);
    }

    /**
     * Returns the file manager application component.
     * @return FileManager the component.
     */
    protected function getFileManager()
    {
        if (isset($this->_fileManager)) {
            return $this->_fileManager;
        } else {
            return $this->_fileManager = Yii::app()->getComponent($this->componentID);
        }
    }
}
