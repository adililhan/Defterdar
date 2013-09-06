<?php

class Defterdar {
    
    public $executer = 'shell_exec';
    
    private $tags, $logs, $output, $file;


    public function __construct() {
        if( ! function_exists($this->executer)) {
            throw new Exception('This function has been disabled.');
        }
    }
    
    public function getTags($executer) {
        
        $this->tags = array_filter(explode("\n", $executer('git tag | sort -u')));
        return $this;
        
    }
    
    public function getLogs($executer) {
        
        $nextTag = null;

        $count = count($this->tags);

        if($count === 0) {
            throw new Exception('Does not have any tag.');
        }

        for($i = 0;$i < $count; $i++) {
            $this->logs[$this->tags[$i]] = $executer('git log --pretty="%h%x09%an%x09%s" ' . $nextTag . $this->tags[$i] . ' | grep -v "Merge branch"' . "\n\n");
            $nextTag = $this->tags[$i] . '..';
        }
        
        return $this;
        
    }
    
    public function fileContentGenerator() {
        
        $this->logs = array_reverse($this->logs);
        
        reset($this->logs);
        $firstKey = key($this->logs);
        $current = $this->logs[$firstKey];
        
        unset($this->logs[$firstKey]);
        
        $this->output = null;
        
        foreach($this->logs as $tag => $commits) {
            $this->output .= '#### [' . $tag . ']' . "\n";
            $this->output .= $commits;
            $this->output .= "\n";
        }
        
        $this->output = '#### [Current]' . "\n" . $current . "\n" . $this->output;
        
        return $this;
        
    }
    
    public function markDownGenerator() {
        $this->output = preg_replace('/^(.*?)\t/m', ' * [$1](../../commit/$1)', $this->output);
        $this->output = preg_replace('/#([0-9])/m', ' [#$1](../../issues/$1)', $this->output);
        $this->output = preg_replace('/\)(.*?)\t/m', ') - __($1)__ ', $this->output);
        
        return $this;
    }
    
    public function fileGenerate() {
        $this->file = fopen('CHANGELOG.md', 'w+');
        if ( ! $this->file) {
            throw new Exception('Unable to create file.');
        }
        
        fwrite($this->file, $this->output);
        fclose($this->file);
    }
    
}

try {
    $git = new Defterdar;
    $executer = $git->executer;
    $git->getTags($executer)
        ->getLogs($executer)
        ->fileContentGenerator()
        ->markDownGenerator()
        ->fileGenerate();
} catch(Exception $e) {
    echo $e->getMessage();
}
