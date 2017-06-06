<?php

namespace Mypos\IPC;

/**
 * IPC Configuration class
 */
class Config{

	private $privateKey = null;
	private $APIPublicKey = null;
    private $encryptPublicKey = null;
	private $keyIndex = null;
	private $sid;
	private $wallet;
	private $lang = 'en';
	private $version = '1.3';
	private $ipc_url = 'https://www.mypos.eu/vmp/checkout';
    private $developerKey;

	/**
	 * Store private RSA key
	 * @param string $privateKey
	 * @return Config
	 */
	public function setPrivateKey($privateKey){
		$this->privateKey = $privateKey;
		return $this;
	}

	/**
	 * Store private RSA key
	 * @return type
	 */
	public function getPrivateKey(){
		return $this->privateKey;
	}

	/**
	 * Store private RSA key as a filepath
	 * @param string $path File path
	 * @return Config
	 * @throws IPC_Exception
	 */
	public function setPrivateKeyPath($path){
		if(!is_file($path) || !is_readable($path)){
			throw new IPC_Exception('Private key not found in:' . $path);
		}
		$this->privateKey = file_get_contents($path);
		return $this;
	}

	/**
	 * IPC API public RSA key
	 * @param string $publicKey
	 * @return Config
	 */
	public function setAPIPublicKey($publicKey){
		$this->APIPublicKey = $publicKey;
		return $this;
	}

	/**
	 * IPC API public RSA key
	 * @return string
	 */
	public function getAPIPublicKey(){
		return $this->APIPublicKey;
	}

	/**
	 * IPC API public RSA key as a filepath
	 * @param string $path
	 * @return Config
	 * @throws IPC_Exception
	 */
	public function setAPIPublicKeyPath($path){
		if(!is_file($path) || !is_readable($path)){
			throw new IPC_Exception('Public key not found in:' . $path);
		}
		$this->APIPublicKey = file_get_contents($path);
		return $this;
	}



    /**
     * Public RSA key using for encryption sensitive data
     * @param string $privateKey
     * @return Config
     */
    public function setEncryptPublicKey($key){
        $this->encryptPublicKey = $key;
        return $this;
    }

    /**
     * Public RSA key using for encryption sensitive data
     * @return type
     */
    public function getEncryptPublicKey(){
        return $this->encryptPublicKey;
    }

    /**
     * Public RSA key using for encryption sensitive data
     * @param string $path File path
     * @return Config
     * @throws IPC_Exception
     */
    public function setEncryptPublicKeyPath($path){
        if(!is_file($path) || !is_readable($path)){
            throw new IPC_Exception('Key not found in:' . $path);
        }
        $this->encryptPublicKey = file_get_contents($path);
        return $this;
    }


    /**
	 * Keyindex used for signing request
	 * @param int $keyIndex
	 * @return Config
	 */
	public function setKeyIndex($keyIndex){
		$this->keyIndex = $keyIndex;
		return $this;
	}

	/**
	 *  Keyindex used for signing request
	 * @return type
	 */
	public function getKeyIndex(){
		return $this->keyIndex;
	}

	/**
	 * Store ID
	 * @return int
	 */
	public function getSid(){
		return $this->sid;
	}

	/**
	 * Store ID
	 * @param int $sid
	 * @return Config
	 */
	public function setSid($sid){
		$this->sid = $sid;
		return $this;
	}

	/**
	 * Wallet number
	 * @param string $wallet
	 * @return Config
	 */
	public function setWallet($wallet){
		$this->wallet = $wallet;
		return $this;
	}

	/**
	 * Wallet number
	 * @return string
	 */
	public function getWallet(){
		return $this->wallet;
	}

	/**
	 * Language code (ISO 639-1)
	 * @param string $lang
	 * @return Config
	 */
	public function setLang($lang){
		$this->lang = $lang;
		return $this;
	}

	/**
	 * Language code (ISO 639-1)
	 * @return string
	 */
	public function getLang(){
		return $this->lang;
	}

	/**
	 * API Version
	 * @param string $version
	 * @return Config
	 */
	public function setVersion($version){
		$this->version = $version;
		return $this;
	}

	/**
	 * API Version
	 * @return string
	 */
	public function getVersion(){
		return $this->version;
	}

	/**
	 * IPC API URL
	 * @return string
	 */
	public function getIpcURL(){
		return $this->ipc_url;
	}

	/**
	 * IPC API URL
	 * @param string $ipc_url
	 * @return Config
	 */
	public function setIpcURL($ipc_url){
		$this->ipc_url = $ipc_url;
		return $this;
	}

    /**
	 * Set myPOS developer key.
	 * @param string $developerKey
	 * @return Config
	 */
	public function setDeveloperKey($developerKey){
		$this->developerKey = $developerKey;
		return $this;
	}

	/**
	 * Store private RSA key
	 * @return type
	 */
	public function getDeveloperKey(){
		return $this->developerKey;
	}

	/**
	 * Validate all set config details
	 * @return boolean
	 * @throws IPC_Exception
	 */
	public function validate(){
		if($this->getKeyIndex() == null || !is_numeric($this->getKeyIndex())){
			throw new IPC_Exception('Invalid Key Index');
		}

		if($this->getIpcURL() == null || !Helper::isValidURL($this->getIpcURL())){
			throw new IPC_Exception('Invalid IPC URL');
		}

		if($this->getSid() == null || !is_numeric($this->getSid())){
			throw new IPC_Exception('Invalid SID');
		}

		if($this->getWallet() == null || !is_numeric($this->getWallet())){
			throw new IPC_Exception('Invalid Wallet number');
		}

		if($this->getVersion() == null){
			throw new IPC_Exception('Invalid IPC Version');
		}

		if(!openssl_get_privatekey($this->getPrivateKey())){
			throw new IPC_Exception('Invalid Private key');
		}
		return true;
	}

}
