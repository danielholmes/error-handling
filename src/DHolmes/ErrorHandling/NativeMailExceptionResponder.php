<?php

namespace DHolmes\ErrorHandling;

use Exception;

class NativeMailExceptionResponder implements ExceptionResponder
{
    /** @var string */
    private $toEmailAddress;
    /** @var string */
    private $subjectFormat;
    /** @var string */
    private $fromEmailAddress;
    
    /**
     * @param string $fromEmailAddress
     * @param string $toEmailAddress
     * @param string $subjectFormat 
     */
    public function __construct($fromEmailAddress, $toEmailAddress, 
        $subjectFormat = 'Error Encountered on %s')
    {
        $this->fromEmailAddress = $fromEmailAddress;
        $this->toEmailAddress = $toEmailAddress;
        $this->subjectFormat = $subjectFormat;
    }
    
    /** @return string */
    public function getToEmailAddress()
    {
        return $this->toEmailAddress;
    }
    /** @param string $toEmailAddress */
    public function setToEmailAddress($toEmailAddress)
    {
        $this->toEmailAddress = $toEmailAddress;
    }
    
    /** @return string */
    public function getSubjectFormat()
    {
        return $this->subjectFormat;
    }
    /** @param string $subjectFormat */
    public function setSubjectFormat($subjectFormat)
    {
        $this->subjectFormat = $subjectFormat;
    }
    
    /** @return string */
    public function getFromEmailAddress()
    {
        return $this->fromEmailAddress;
    }
    /** @param string $fromEmailAddress */
    public function setFromEmailAddress($fromEmailAddress)
    {
        $this->fromEmailAddress = $fromEmailAddress;
    }
    
    /** @param Exception */
    public function respond(Exception $e)
    {        
        $content = (string)$e . "\n";
        $content .= 'SERVER: ' . print_r($_SERVER, true) . "\n";
        $content .= 'POST: ' . print_r($_POST, true) . "\n";
        $content .= 'FILES: ' . print_r($_FILES, true) . "\n";

        $url = '';
        if (isset($_SERVER['HTTP_HOST']) && isset($_SERVER['REQUEST_URI']))
        {
            $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }
        
        $headers = sprintf('From: %s', $this->fromEmailAddress);
        $subject = sprintf($this->subjectFormat, $url);
        @mail($this->toEmailAddress, $subject, $content, $headers);
    }
}