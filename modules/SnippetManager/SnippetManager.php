<?php
//引用此页面前需先引用conf.php
error_reporting(E_ALL ^ E_DEPRECATED);


class SnippetManager extends Manager{
    public function info()
    {
        return "SnippetManager"; // TODO: Change the autogenerated stub
    }
	
	public $config = [
		'viewPath'=>'../TinydreamWeb/view',
        'templatePath'=>'../TinydreamUConfig',//默认模板路径
	];

	public function SnippetManager(){
		
	}
	
	public function BuildSnippets($datas){
		$path = $this->config['viewPath'];
		if(isset($_REQUEST['url'])){
			$path=$_REQUEST['url'];
		}
		$result = [];
		$datas = json_decode($datas,true);
		foreach($datas as $data){
			$result[$data['name']] = $this->SingleSnippet($path,$data['name'],json_encode($data['data']));
		}
		$backMsg = RESPONDINSTANCE('0');
		$backMsg['snippet']=$result;
		return $backMsg;
	}
	
	public function SingleSnippet($path,$name,$data){
		$fullPath = $path.'/'.$name;
		$endfix = substr($fullPath,strrpos($fullPath,'.')+1);
		if($endfix != 'html'){
			$fullPath = $fullPath.'.html';
		}
		$data = json_decode($data,true);
		/*if(!file_exists($fullPath)){
			return "未定义:'".$name."'#LB#/br#RB#";
		}*/
		$template = file_get_contents($fullPath);
		$result = '';
		foreach($data as $seek=>$index){
			$current = $template;
			foreach($index as $key=>$value){
				$current = str_replace("{{{$key}}}",$value,$current);
			}
			$result = $result.$current;
		}
		$result = str_replace("<","#LB#",$result);
		$result = str_replace(">","#RB#",$result);
		return $result;
	}
	
	public function BuildSnippet($name,$data){
		$path = $this->config['viewPath'];
		if(isset($_REQUEST['url'])){
			$path=$_REQUEST['url'];
		}
		$backMsg = RESPONDINSTANCE('0');
		$backMsg['snippet'][$name]=$this->SingleSnippet($path,$name,$data);
		return $backMsg;
	}

	public static function GetAttributeFromData($turl,$key){
        $templatePath = (new SnippetManager())->config['templatePath'];

        $fullPath = $templatePath.'/'.$turl.'.php';

        $data = [];

        if(isset($_REQUEST['root'])){
            $templatePath = $_REQUEST['root'];
        }


        if(!file_exists($fullPath)){
            return RESPONDINSTANCE('77',$turl)['context'];
        }
        include($fullPath);
		
        return $data[$key];

    }
	
    //获取模板列表
	public static function GetTemplateList(){
		$SM = new SnippetManager();
		return $SM->TemplateList()['list'];
	}

    //获取模板列表
    public function TemplateList(){
        $templatePath = $this->config['templatePath'];
        $file = scandir($templatePath);
        $list = [];
        foreach ($file as $filename) {
			$explode = explode(".",$filename);
			$end = end($explode);
           /* $endName = end(explode(".",$filename));*/
            if($end == 'php' && $filename != "pull.php"){
                array_push($list,array_shift($explode));
            }
        }
		$backMsg = RESPONDINSTANCE('0');
		$backMsg['list'] = $list;
        return $backMsg;
    }

	public function BuildTemplate($turl){
	    $templatePath = $this->config['templatePath'];


        if(isset($_REQUEST['root'])){
            $templatePath = $_REQUEST['root'];
        }

        $fullPath = $templatePath.'/'.$turl.'.php';

        $data = [];

        if(!file_exists($fullPath)){
            return RESPONDINSTANCE('77',$turl);
        }

        include ($fullPath);

        $templateUrl = $templatePath.'/'.$turl.'.html';
        if(isset($data['template']) && !empty($data['template'])){
            $templateUrl = $templatePath.'/'.$data['template'];
        }

        if(!file_exists($templateUrl)){
            return RESPONDINSTANCE('78',$templateUrl);
        }


        $result = file_get_contents($templateUrl);

        foreach($data as $key=>$value){
            $result = str_replace("{{{$key}}}",$value,$result);
        }

        $result = str_replace("<","#LB#",$result);

        $result = str_replace(">","#RB#",$result);

        $backMsg = RESPONDINSTANCE('0');

        $backMsg['layout'] = $result;

        return $backMsg;
    }
}
?>