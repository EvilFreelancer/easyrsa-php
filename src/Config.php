<?php

namespace EasyRSA;

class Config
{
    /**
     * Name of file (with or without full path to this file)
     * @var string
     */
    private $_archive = './easy-rsa.tar.gz';

    /**
     * Path to folder with easy-rsa scripts
     * @var string
     */
    private $_folder = './easy-rsa';

    /**
     * Path to folder with certificates
     * @var string
     */
    private $_certs_folder = '.';

    /**
     * @return string
     */
    public function getCertsFolder(): string
    {
        return $this->_certs_folder;
    }

    /**
     * @param string $folder
     * @return  Config
     */
    public function setCertsFolder(string $folder): self
    {
        $this->_certs_folder = $folder;
        return $this;
    }

    /**
     * @return string
     */
    public function getFolder(): string
    {
        return $this->_folder;
    }

    /**
     * Set path with easy-rsa scripts
     *
     * @param   string $folder
     * @return  Config
     */
    public function setFolder(string $folder): self
    {
        $this->_folder = $folder;
        return $this;
    }

    /**
     * @return string
     */
    public function getArchive(): string
    {
        return $this->_archive;
    }

    /**
     * Set file with full path
     *
     * @param   string $archive
     * @return  Config
     */
    public function setArchive(string $archive): self
    {
        $this->_archive = $archive;
        return $this;
    }
}
