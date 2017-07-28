<?php
namespace Home\Controller;
use Think\Controller;
class TaskController extends Controller {

	public function index(){

		//var_dump($_SESSION);die;
		

		//$user = D('user');
		//$uData = $user->get_userinfo_by_id($_SESSION['userid']);
		//$this->assign('uData',$uData);//var_dump($uData);die;
		$this->display('index');
	}

	public function getAjaxCat(){
		$task = D('task');
		$proData = $task->getProject();
		if(!empty($proData)){
			echo json_encode($proData);
		}else{
			echo 'err';
		}
		
		//$this->assign('proData',$proData);

		//$faceData = $task->getWorkFace();
	}

	public function getAjaxFace(){
		$task = D('task');
		$proData = $task->getWorkFace();
		if(!empty($proData)){
			echo json_encode($proData);
		}else{
			echo 'err';
		}
		
		
	}

	function showAddTask(){
		$task = D('task');
		$proData = $task->getProject();
		$this->assign('proData',$proData);

		$user = D('user');
		$userdata = $user->field('id,name')->select();
		$this->assign('userdata',$userdata);
		
		//工种
		$jobtype = M('jobtype');
		$jData = $jobtype->select();
		$this->assign('jData',$jData);
		// // 材料
		// $materialstore = M('material');
		// $mData = $materialstore->select();
		// $this->assign('mData',$mData);
		// // 设备
		// $equip = M('equip');
		// $eData = $equip->select();
		// $this->assign('eData',$eData);

		// 材料
		$mterialstore = M('material');
		$mData = $mterialstore->field('name,model,unit')->group('name')->select();
		$this->assign('mData',$mData);
		$dataObj = $mterialstore->field('name,model,unit')->select();
		$this->assign('dataObj',json_encode($dataObj));
		// 设备
		$equip = M('equip');
		$eData = $equip->field('name,model,unit')->group('name')->select();
		$this->assign('eData',$eData);
		$datadObj = $equip->field('name,model,unit')->select();
		$this->assign('datadObj',json_encode($datadObj));

		$this->display();
	}

	public function addTask(){
		
		$task = D('task');
		
		$taskfid = M('taskfid');
		

		if(isset($_GET['id'])&&!empty($_GET['id'])){
			$data['fid']=$_GET['id'];
			$rd['is_last']=0;
			$task->where('id='.$_GET['id'])->save($rd);
		}else{
			$data['fid']=0;
		}
		$taskfid->add($data);
		$data['checkstatus']=0;
		$data['taskname']=$_POST['taskname'];
		$data['starttime']=$_POST['starttime'];
		$data['endtime']=$_POST['endtime'];
		$data['userid']=$_SESSION['userid'];
		$data['recervice']=$_SESSION['userid'];
		$data['project_id']=$_POST['project_id'];
		$data['status']=1;
		$data['is_last']=1;
		$data['supply_num']=$_POST['supply_num'];
		$data['unit']=$_POST['tunit'];
		$data['director']=$_POST['director'];
		$data['mainproject_id']=$_SESSION['project'];
		$res = $task->add($data);
		$hresources = M('hresources');
		$mresource = M('mresource');
		$dresource = M('dresource');
		if(!empty($_POST['wname'])){
			foreach($_POST['wname'] as $kwtype=>$v){
				$hdata['wname']=$_POST['wname'][$kwtype];
				$hdata['unit']=$_POST['unit'][$kwtype];
				$hdata['num']=$_POST['num'][$kwtype];
				$hdata['taskid']=$res;
				$hresources->add($hdata);
			}
		}

		if(!empty($_POST['mname'])){
			foreach($_POST['mname'] as $mk=>$mv){
				if(!empty($mv)){
					$mdata['mname']=$_POST['mname'][$mk];
					$mdata['mtype']=$_POST['mtype'][$mk];
					$mdata['munit']=$_POST['munit'][$mk];
					$mdata['mnum']=$_POST['mnum'][$mk];
					$mdata['taskid']=$res;
					$mresource->add($mdata);
				}
			}
		}
		
		if(!empty($_POST['dname'])){
			foreach($_POST['dname'] as $dk=>$dv){
				if(!empty($dv)){
					$ddata['dname']=$_POST['dname'][$dk];
					$ddata['dunit']=$_POST['dunit'][$dk];
					$ddata['dnum']=$_POST['dnum'][$dk];
					$ddata['dtype']=$_POST['dtype'][$dk];
					$ddata['usedate']=$_POST['usedate'][$dk];
					$ddata['taskid']=$res;
					$dresource->add($ddata);
				}
			}
		}
		
		
		
	}



	//获得待发送的任务
	public function sendTask(){
		header('Access-Control-Allow-Origin:*'); 
		$task = D('task');
		$data = $task->getTask(1);
		// var_dump($data);
		
		$this->assign('sendTaskData',$data);
		$this->display('sendTask');
		
	}

	public function showSendTask($id){
		$task=D('task');
		$data = $task->getTaskDetail($id);
		if(!empty($data)){
			$hresources = M('hresources');
			$mresource = M('mresource');
			$dresource = M('dresource');

			$hData = $hresources->where('taskid='.$id.' and style=0')->select();
			$this->hData = $hData;
			$mData = $mresource->where('taskid='.$id.' and style=0')->select();
			$this->mData = $mData;
			$dData = $dresource->where('taskid='.$id.' and style=0')->select();
			$this->dData = $dData;
			$user = D('user');
			$duser = $user->get_userinfo_by_id($data['director']);
			$this->duser=$duser;

			$project	=	M('project');
			$proData = $project->where('id='.$data['project_id'])->field('proname')->find();
			$this->assign('proData',$proData);
		}

		$this->data = $data;
		$this->display();
	}

	public function showEditTask($id){
		$task=D('task');
		$data = $task->getTaskDetail($id);
		if(!empty($data)){
			$hresources = M('hresources');
			$mresource = M('mresource');
			$dresource = M('dresource');

			//工种
			$jobtype = M('jobtype');
			$jData = $jobtype->field('name')->select();
			$this->assign('jData',$jData);
			// 材料
			$materialstore = M('material');
			$maData = $materialstore->field('name,model,unit')->group('name')->select();
			$this->assign('maData',$maData);
			$dataObj = $materialstore->field('name,model,unit')->select();
			$this->assign('dataObj',json_encode($dataObj));
			// 设备
			$equip = M('equip');
			$eData = $equip->field('name,model,unit')->group('name')->select();
			$this->assign('eData',$eData);
			$datadObj = $equip->field('name,model,unit')->select();
			$this->assign('datadObj',json_encode($datadObj));
			

			$hData = $hresources->where('taskid='.$id.' and style=0')->select();
			$this->hData = $hData;
			$mData = $mresource->where('taskid='.$id.' and style=0')->select();
			$this->mData = $mData;
			$dData = $dresource->where('taskid='.$id.' and style=0')->select();
			$this->dData = $dData;

			$user = D('user');
			$userdata = $user->field('id,name')->select();
			$this->assign('userdata',$userdata);

			$project	=	M('project');
			$proData = $project->field('id,proname')->select();
			$this->assign('proData',$proData);
		}

		$this->data = $data;
		$this->display();
	}

	function getType(){
		$materialstore = M('material');
		$maData = $materialstore->field('name,model,unit')->select();
		echo json_encode($maData);
	}

	public function editTask($id){

		$task = D('task');
		
		$taskfid = M('taskfid');
		
	
		$data['taskname']=$_POST['taskname'];
		$data['starttime']=$_POST['starttime'];
		$data['endtime']=$_POST['endtime'];
		//$data['userid']=$_SESSION['userid'];
		$data['project_id']=$_POST['project_id'];
		//$data['status']=1;
		//$data['is_last']=0;
		$data['supply_num']=$_POST['supply_num'];
		$data['unit']=$_POST['tunit'];
		$data['director']=$_POST['director'];
		
		$res = $task->where('id='.$id)->save($data);

		$hresources = M('hresources');
		$mresource = M('mresource');
		$dresource = M('dresource');
		$hresources->where('taskid='.$id)->delete();
		$dresource->where('taskid='.$id)->delete();
		$mresource->where('taskid='.$id)->delete();
		if(!empty($_POST['wname'])){
			foreach($_POST['wname'] as $kwtype=>$v){
				$hdata['wname']=$_POST['wname'][$kwtype];
				$hdata['unit']=$_POST['unit'][$kwtype];
				$hdata['num']=$_POST['num'][$kwtype];
				$hdata['taskid']=$id;
				$hresources->add($hdata);
			}
		}

		if(!empty($_POST['mname'])){
			foreach($_POST['mname'] as $mk=>$mv){
				if(!empty($mv)){
					$mdata['mname']=$_POST['mname'][$mk];
					$mdata['mtype']=$_POST['mtype'][$mk];
					$mdata['munit']=$_POST['munit'][$mk];
					$mdata['mnum']=$_POST['mnum'][$mk];
					$mdata['taskid']=$id;
					$mresource->add($mdata);
				}
			}
		}
		
		if(!empty($_POST['dname'])){
			foreach($_POST['dname'] as $dk=>$dv){
				if(!empty($dv)){
					$ddata['dname']=$_POST['dname'][$dk];
					$ddata['dunit']=$_POST['dunit'][$dk];
					$ddata['dnum']=$_POST['dnum'][$dk];
					$ddata['dtype']=$_POST['dtype'][$dk];
					$ddata['usedate']=$_POST['usedate'][$dk];
					$ddata['taskid']=$id;
					$dresource->add($ddata);
				}
			}
		}
	}

	public function showStopAllTask($id){
		$task = M('task');
		$resolvedata = $task->where('id='.$id)->find();
		$this->resolvedata = $resolvedata;
		$this->display();
	
	}
	public function stopAllTask($id,$stop){
		$task = D('task');
		$data['stop']=$stop;
		$tdata= $task->where('id='.$id)->save($data);

		$res = $this->getAllTask($id,$stop);
		
		echo 'ok';
	
	}

	public function getAllTask($fid,$stop){
		$task = M('task');
		$result=array();
		$sdata['stop']=$stop;
		$data = $task->where('fid='.$fid)->field('id,fid')->select();
		foreach($data as $v){
			if($v['fid']==$fid){
				
				$task->where('fid='.$fid)->save($sdata);
				
				$this->getAllTask($v['id'],$stop);
			}
		}
		
	}
	

	// 获得待启动任务
	public function startTask(){
		$task = D('task');
		$data = $task->getTask(2);
		$this->assign('sendTaskData',$data);
		$this->display('startTask');
	}
	// 测试手机端
// 	public function startTask(){
// 		$task = D('task');
// 		$data = $task->getTask(2,0);
// 		if(!empty($data)){
// 			foreach($data as $kData=>$vData){
				
// 				$arr = $task->getTask(2,$vData['id']);
// 				foreach($arr as $k=>$v){
				
// 					if($v['status']==2){
// 						$d[$v['proname']][]=$v;
// 					}
					
// 				}
// 				if(!empty($d)){
// 					$startTask[$vData['id']][0]=$vData;
// 					foreach($d as $kd=>$vd){
// 							foreach($vd as $kvd=>$vvd){
// 								if($kd==$vvd['proname'] && $vvd['fid']==$vData['id']){
								
							
// 								$tadata = $task->where('fid='.$vvd['id'])->select();
//                                  if(!empty($tadata)){
//                                  	 foreach($tadata as $skd=>$svd){
//                                  	 	 if($svd['status']==2){
//                                  	 	 	$vvd['smallTask'][]=$svd;

//                                  	 	 }
//                                  	 }
//                                  	 $startTask[$vData['id']][1][$vvd['proname']][]=$vvd;
//                                  }
// 							}
							
// 						}
						
// 					}
// 				}
			
// 			}
// 		}
// // var_dump($startTask);
// 		$this->assign('startTaskData',$startTask);
// 		$this->display('startTask');
// 	}

	//开启任务
	public function startToTask($id,$fid,$status){
	
		$task = D('task');
		$data['status']=$status;
		if($status=='3'){
			$data['realstarttime']=date('Y-m-d',time());
			
		}

		$aarr = $this->getAllFid($id);
		foreach($aarr as $varr){
			if($varr['userid']!=$_SESSION['userid']){
				//if($varr['fid']!=0){
					$task->where('id='.$varr['id'])->save($data);
				//}
				
				$atadata = $task->where('fid='.$varr['fid'])->field('status')->select();
				
				if(!empty($atadata)){
					foreach($atadata as $k=>$v){
						$sarr[]=$v['status'];
					}
					$astr='';
					if(in_array(1, $sarr)){
						$astr.='1,';
					}
					if(in_array(2, $sarr)){
						$astr.='2,';
					}
					if(in_array(3, $sarr)){
						$astr.='3,';
					}
					if(in_array(4, $sarr)){
						$astr.='4,';
					}
					if(in_array(5, $sarr)){
						$astr.='5';
					}
					$aadata['status']=$astr;

					$task->where('id='.$varr['id'])->save($aadata);
					//echo $task->_sql();
				}
			}

		}
		
		$task->where('id='.$id)->save($data);
		
		$tadata = $task->where('fid='.$fid)->field('status')->select();
		if(!empty($tadata)){
			foreach($tadata as $k=>$v){
				$arr[]=$v['status'];
			}
			$str='';
			if(in_array(1, $arr)){
				$str.='1,';
			}
			if(in_array(2, $arr)){
				$str.='2,';
			}
			if(in_array(3, $arr)){
				$str.='3,';
			}
			if(in_array(4, $arr)){
				$str.='4,';
			}
			if(in_array(5, $arr)){
				$str.='5';
			}
			$adata['status']=$str;
			$res = $task->where('id='.$fid)->save($adata);
			//echo $task->_sql();
			
		}
		
	}
	//查找所有父ID
	public function getAllFid($id,&$result=array()){
		$task = M('task');
		$data = $task->where('id='.$id)->field('fid,userid,recervice,id')->select();
		if(!empty($data)){
			foreach($data as $v){
				// $row['fid']=$v['fid'];
				// $row['userid']=$v['userid'];
				$result[]=$v;
				
				$this->getAllFid($v['fid'],$result);
			}
			return $result;
		}
	}

	//进行中任务
	public function runTask(){
		$task = D('task');
		$data = $task->getTask(3);
		$this->assign('sendTaskData',$data);
		$this->display('runTask');
	}



	// 暂停进行中的任务
	function stopToTask($id,$fid){
		$task = D('task');
		$data['status']=4;
		$res = $task->where('id='.$id)->save($data);
		$tadata = $task->where('fid='.$fid)->field('status')->select();
		if(!empty($tadata)){
			foreach($tadata as $k=>$v){
				$arr[]=$v['status'];
			}
			$str='';
			if(in_array(2, $arr)){
				$str.='2,';
			}
			if(in_array(3, $arr)){
				$str.='3,';
			}
			if(in_array(4, $arr)){
				$str.='4,';
			}
			if(in_array(5, $arr)){
				$str.='5';
			}
			$adata['status']=$str;
			$res = $task->where('id='.$fid)->save($adata);
			if($res){
				echo 'ok';
			}
		}

	}

	//暂停的任务
	public function stopTask(){
		$task = D('task');
		$data = $task->getTask(4);
		$this->assign('sendTaskData',$data);
		$this->display('stopTask');
	}





	//完成的任务
	
	public function finishTask(){
		$task = D('task');
		$data = $task->getTask(5);
		$this->assign('sendTaskData',$data);
		$this->display('finishTask');
	}


	//分解任务
	

	public function get_user(){
		$user = D('user');
		$urdata = $user->get_pro_manager(3);
		echo json_encode($urdata);
	}

	
	
	public function sendTaskToUser($id){
		
		
			$task = M('task');
			$resolvedata = $task->where('id='.$id)->find();
			$this->resolvedata = $resolvedata;

			$user = D('user');
			$urdata = $user->get_pro_manager(4);
			$this->urdata = $urdata;

			$this->display();
		
	}

	//推送任务给生产经理
	public function stToUser($id,$userid){
			$task = M('task');

			$data['recervice']=$userid;
			$res = $task->where('id='.$id)->save($data);
			$taskfid = M('taskfid');
			$fdata['userid']=$userid;
			$arr = $task->where('id='.$id)->field('fid')->find();

			$tdata = $taskfid->where('fid='.$arr['fid']. ' and userid='.$userid)->select();
			if(empty($tdata)){
				$fdata['fid']=$arr['fid'];
				$fdata['userid']=$userid;
				$res = $taskfid->add($fdata);
			}
			
		
			if($res){
				echo 'ok';
			}
	}

	

	public function editsmallTask($id){
		$task = D('task');
		$data = $task->geteditsmallTask($id);
		if(!empty($data)){
			foreach($data as $kData=>$vData){
				
				$d[$vData['project_id']][]=$vData;
				
			
			}

		}
		echo json_encode($d);
	}

	public function editTaskForm($id){
		$task=D('task');
		$task->delTask($id);

		$data['taskname']=$_POST['bigtaskname'];
		$data['starttime']=$_POST['bigstarttime'];
		$data['endtime']=$_POST['bigendtime'];
		$data['userid']=$_SESSION['userid'];
		$data['fid']=0;
		$d=$_POST['sproid'];
		$ids = $task->where('id='.$id)->save($data);
	
		for($i=0;$i<=$d;$i++){
			foreach($_POST['proname'.$i]['taskname'] as $k=>$v){
				$rdata['taskname']=$_POST['proname'.$i]['taskname'][$k];
				$rdata['starttime']=$_POST['proname'.$i]['starttime'][$k];
				$rdata['endtime']=$_POST['proname'.$i]['endtime'][$k];
				$rdata['userid']=$_SESSION['userid'];
				$rdata['fid']=$id;
				$rdata['project_id']=$_POST['proname'.$i]['proname'][0];
				$rdata['workface_id']=$_POST['proname'.$i]['facename'][$k];
				$task->add($rdata);
			}
		}

	}

	// 查看待启动任务详情
	public function showStartTask($id){
		$task=D('task');
		$data = $task->getTaskDetail($id);
		if(!empty($data)){
			$hresources = M('hresources');
			$mresource = M('mresource');
			$dresource = M('dresource');

			$hData = $hresources->where('taskid='.$id.' and style=0')->select();
			$this->hData = $hData;
			$mData = $mresource->where('taskid='.$id.' and style=0')->select();
			$this->mData = $mData;
			$dData = $dresource->where('taskid='.$id.' and style=0')->select();
			$this->dData = $dData;

		}

		$this->data = $data;
		$this->display();
	}

	// 查看进行中任务详情
	public function showRunTask($id){
		$task=D('task');
		$data = $task->getTaskDetail($id);
		if(!empty($data)){
			$hresources = M('hresources');
			$mresource = M('mresource');
			$dresource = M('dresource');

			$hData = $hresources->where('taskid='.$id.' and style=0')->select();
			$this->hData = $hData;
			$mData = $mresource->where('taskid='.$id.' and style=0')->select();
			$this->mData = $mData;
			$dData = $dresource->where('taskid='.$id.' and style=0')->select();
			$this->dData = $dData;
			$user = D('user');
			$duser = $user->get_userinfo_by_id($data['director']);
			$this->duser=$duser;

			$project	=	M('project');
			$proData = $project->where('id='.$data['project_id'])->field('proname')->find();
			$this->assign('proData',$proData);
		}

		$this->data = $data;
		$this->display();
	}

	// 查看进行中任务详情
	public function showStopTask($id){
		$task=D('task');
		$data = $task->getTaskDetail($id);
		if(!empty($data)){
			$hresources = M('hresources');
			$mresource = M('mresource');
			$dresource = M('dresource');

			$hData = $hresources->where('taskid='.$id.' and style=0')->select();
			$this->hData = $hData;
			$mData = $mresource->where('taskid='.$id.' and style=0')->select();
			$this->mData = $mData;
			$dData = $dresource->where('taskid='.$id.' and style=0')->select();
			$this->dData = $dData;
			$user = D('user');
			$duser = $user->get_userinfo_by_id($data['director']);
			$this->duser=$duser;

			$project	=	M('project');
			$proData = $project->where('id='.$data['project_id'])->field('proname')->find();
			$this->assign('proData',$proData);
		}

		$this->data = $data;
		$this->display();
	}
	// 查看进行中任务详情
	public function showFinishTask($id){
		$task=D('task');
		$data = $task->getTaskDetail($id);
		if(!empty($data)){
			$hresources = M('hresources');
			$mresource = M('mresource');
			$dresource = M('dresource');

			$hData = $hresources->where('taskid='.$id.' and style=0')->select();
			$this->hData = $hData;
			$mData = $mresource->where('taskid='.$id.' and style=0')->select();
			$this->mData = $mData;
			$dData = $dresource->where('taskid='.$id.' and style=0')->select();
			$this->dData = $dData;
			$user = D('user');
			$duser = $user->get_userinfo_by_id($data['director']);
			$this->duser=$duser;

			$project	=	M('project');
			$proData = $project->where('id='.$data['project_id'])->field('proname')->find();
			$this->assign('proData',$proData);
		}

		$this->data = $data;
		$this->display();
	}

	public function addSteels(){
		
		header('Access-Control-Allow-Origin:*');
		$user = D('userid');
		$userd = $user->where('id='.$_POST['userid'])->field('name,id')->find();
		
		if(!empty($userd)){
			$halfproductrecord = M('halfproductrecord');
			$data['materialId']=$_POST['materialId'];
			$data['count']=$_POST['count'];
			$data['usepart']=$_POST['usepart'];
			$data['factory_num']=$_POST['factory_num'];
			$data['addtime']=date('Y-m-d',time());
			$data['dyty_name']=$userd['name'];
			$data['hunit']=$_POST['hunit'];
			$data['process_time']=$_POST['process_time'];
			$res = $halfproductrecord->add($data);
			if($res){
				echo json_encode(array(1));die;
			}else{
				echo json_encode(array(0));die;
			}
		}else{
			echo json_encode(array(3));die;
		}
		
	}


	public function getTaskProcess(){
		//$task = M('task');
		//$hData = $task->where(' (userid='.$_SESSION['userid'].' or recervice='.$_SESSION['userid'].')  and stop=0 and is_last=1 and mainproject_id='.$_SESSION['project'])->field('id,taskname,starttime,endtime,recervice,task_process')->select();
		
		$this->display();
	}

	public function showgetTaskProcess(){

		$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
		$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
	
		if(isset($_GET['userid'])){
			$count = M()->table('__TASK__ t')->join(' LEFT JOIN __USER__ u on u.id=t.recervice')->where(' (t.userid='.$_GET['userid'].' or t.recervice='.$_GET['userid'].')  and t.stop=0 and t.is_last=1 and t.mainproject_id='.$_GET['project'])->count();
		
			$result = M()->table('__TASK__ t')->join(' LEFT JOIN __USER__ u on u.id=t.recervice')->where(' (t.userid='.$_GET['userid'].' or t.recervice='.$_GET['userid'].')  and t.stop=0 and t.is_last=1 and t.mainproject_id='.$_GET['project'])->field('t.id,t.taskname,t.starttime,t.endtime,t.recervice,t.task_process,u.name')->limit(($page-1)*$rows,$rows)->select();
		}else{
			$count = M()->table('__TASK__ t')->join(' LEFT JOIN __USER__ u on u.id=t.recervice')->where(' (t.userid='.$_SESSION['userid'].' or t.recervice='.$_SESSION['userid'].')  and t.stop=0 and t.is_last=1 and t.mainproject_id='.$_SESSION['project'])->count();
		
			$result = M()->table('__TASK__ t')->join(' LEFT JOIN __USER__ u on u.id=t.recervice')->where(' (t.userid='.$_SESSION['userid'].' or t.recervice='.$_SESSION['userid'].')  and t.stop=0 and t.is_last=1 and t.mainproject_id='.$_SESSION['project'])->field('t.id,t.taskname,t.starttime,t.endtime,t.recervice,t.task_process,u.name')->limit(($page-1)*$rows,$rows)->select();
		}
		
		$items=array();
		foreach($result as $val){
			if($val['task_process']==0){ 
				$val['task_process']='任务未启动'; 
			}else if($val['task_process']==100){ 
				$val['task_process']='任务已完成';
			}else{ 
				$val['task_process']=$val['task_process'].'%'; 
			}
			array_push($items, $val);
		}
		$hData["rows"] = $items;
		$hData["total"] = $count;
		
		echo json_encode($hData);
	}
	
	public function materialChart(){
		$this->display();
	}

	public function moBanChart(){
		$this->display();
	}
	
	public function muFangChart(){
		$this->display();
	}
	
	public function gangGuanChart(){
		$this->display();
	}
	
	
	
	//甘特图 初始化 
		public function gantuinit(){
	
		$ret= '
			{
				"tasks": [{
						"id": -1,
						"name": "F6标准层墙柱-钢筋",
						"progress": 0,
						"progressByWorklog": false,
						"relevance": 0,
						"type": "",
						"typeId": "",
						"description": "",
						"code": "",
						"level": 0,
						"status": "STATUS_ACTIVE",
						"depends": "",
						"canWrite": true,
						"start": 1500825600000,
						"duration": 20,
						"end": 1501084800000,
						"startIsMilestone": false,
						"endIsMilestone": false,
						"collapsed": false,
						"assigs": [],
						"hasChild": true
					},
					{
						"id": -2,
						"name": "F6标准层墙柱-钢筋",
						"progress": 0,
						"progressByWorklog": false,
						"relevance": 0,
						"type": "",
						"typeId": "",
						"description": "",
						"code": "",
						"level": 1,
						"status": "STATUS_ACTIVE",
						"depends": "",
						"canWrite": true,
						"start": 1501171200000,
						"duration": 10,
						"end": 1501257600000,
						"startIsMilestone": false,
						"endIsMilestone": false,
						"collapsed": false,
						"assigs": [],
						"hasChild": true
					},
					{
						"id": -3,
						"name": "F6标准层混凝土",
						"progress": 0,
						"progressByWorklog": false,
						"relevance": 0,
						"type": "",
						"typeId": "",
						"description": "",
						"code": "",
						"level": 2,
						"status": "STATUS_ACTIVE",
						"depends": "",
						"canWrite": true,
						"start": 1501603200000,
						"duration": 2,
						"end": 1501603200000,
						"startIsMilestone": false,
						"endIsMilestone": false,
						"collapsed": false,
						"assigs": [],
						"hasChild": false
					},
					{
						"id": -4,
						"name": "F6标准层混凝土2",
						"progress": 0,
						"progressByWorklog": false,
						"relevance": 0,
						"type": "",
						"typeId": "",
						"description": "",
						"code": "",
						"level": 2,
						"status": "STATUS_SUSPENDED",
						"depends": "3",
						"canWrite": true,
						"start": 1501603200000,
						"duration": 4,
						"end": 1501603200000,
						"startIsMilestone": false,
						"endIsMilestone": false,
						"collapsed": false,
						"assigs": [],
						"hasChild": false
					},
					{
						"id": -5,
						"name": "F6标准层混凝土3--web",
						"progress": 0,
						"progressByWorklog": false,
						"relevance": 0,
						"type": "",
						"typeId": "",
						"description": "",
						"code": "",
						"level": 1,
						"status": "STATUS_SUSPENDED",
						"depends": "2:5",
						"canWrite": true,
						"start": 1501603200000,
						"duration": 5,
						"end": 1501603200000,
						"startIsMilestone": false,
						"endIsMilestone": false,
						"collapsed": false,
						"assigs": [],
						"hasChild": true
					},
					{
						"id": -6,
						"name": "F6标准层混凝土4 on safari",
						"progress": 0,
						"progressByWorklog": false,
						"relevance": 0,
						"type": "",
						"typeId": "",
						"description": "",
						"code": "",
						"level": 2,
						"status": "STATUS_SUSPENDED",
						"depends": "",
						"canWrite": true,
						"start": 1501171200000,
						"duration": 2,
						"end": 1501257600000,
						"startIsMilestone": false,
						"endIsMilestone": false,
						"collapsed": false,
						"assigs": [],
						"hasChild": false
					},
					{
						"id": -7,
						"name": "F6标准层混凝土25 on ie",
						"progress": 0,
						"progressByWorklog": false,
						"relevance": 0,
						"type": "",
						"typeId": "",
						"description": "",
						"code": "",
						"level": 2,
						"status": "STATUS_SUSPENDED",
						"depends": "6",
						"canWrite": true,
						"start": 1501603200000,
						"duration": 3,
						"end": 1501862400000,
						"startIsMilestone": false,
						"endIsMilestone": false,
						"collapsed": false,
						"assigs": [],
						"hasChild": false
					},
					{
						"id": -8,
						"name": "F6标准层混凝土26 on chrome",
						"progress": 0,
						"progressByWorklog": false,
						"relevance": 0,
						"type": "",
						"typeId": "",
						"description": "",
						"code": "",
						"level": 2,
						"status": "STATUS_SUSPENDED",
						"depends": "6",
						"canWrite": true,
						"start": 1501603200000,
						"duration": 2,
						"end": 1501862400000,
						"startIsMilestone": false,
						"endIsMilestone": false,
						"collapsed": false,
						"assigs": [],
						"hasChild": false
					}
				],
				"selectedRow": 2,
				"deletedTaskIds": [],
				"resources": [{
						"id": "tmp_1",
						"name": "Resource 1"
					},
					{
						"id": "tmp_2",
						"name": "Resource 2"
					},
					{
						"id": "tmp_3",
						"name": "Resource 3"
					},
					{
						"id": "tmp_4",
						"name": "Resource 4"
					}
				],
				"roles": [{
						"id": "tmp_1",
						"name": "Project Manager"
					},
					{
						"id": "tmp_2",
						"name": "Worker"
					},
					{
						"id": "tmp_3",
						"name": "Stakeholder"
					},
					{
						"id": "tmp_4",
						"name": "Customer"
					}
				],
				"canWrite": true,
				"canWriteOnParent": true,
				"zoom": "w3"
			}

	    ';
	    
	    $arr = json_decode($ret,true);
	    echo json_encode($arr);die;
	    
	    
   }
   
   	
	//甘特图 提交数据 
		public function gantuform(){
			print_r($_POST);die;
			
		}
	
}
?>