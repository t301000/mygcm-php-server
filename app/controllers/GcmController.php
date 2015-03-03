<?php

class GcmController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	// 儲存GCM regid
	public function storeGcmId()
	{
		/*
		// 將陣列拆成變數，並檢查必要變數
		extract(Input::all());
		$regid = isset($regid) ? $regid : '';
		$name = isset($name) ? $name : '';
		$email = isset($email) ? $email : '';
		*/
	
		$regid = Input::get('regid', '');
		$name = Input::get('name', '');
		$email = Input::get('email', '');

		// 有缺少資料
		if(!$regid or !$name or !$email){
			$status = array('status' => 'error', 'msg' => '缺少某些欄位');
			return Response::json($status);
		}

		// 依條件取得第一筆物件或new一個新物件
		$gcm = Gcm::firstOrNew(array('regid' => $regid));
		$gcm->name = $name;
		$gcm->email = $email;
		$gcm->save();

		$status = array('status' => 'success', 'id' => $gcm->id);

		return Response::json($status);
	}

	// 刪除GCM regid
	public function deleteGcmId()
	{

		$regid = Input::get('regid');

		Gcm::where('regid', $regid)->delete();

		$status = array('status' => 'success');

		return Response::json($status);
	}

	// 根據GCM regid更新資料
	public function updateData()
	{

		$regid = Input::get('regid');

		$gcm = Gcm::where('regid', $regid)->first();

		$gcm->name = Input::get('name');

		$gcm->email = Input::get('email');

		$gcm->save();

		$status = array('status' => 'success');

		return Response::json($status);
	}

	// 推播訊息
	public function sendMessage()
	{
		$message = Input::get('message');

		// 設定檔：app/config/mygcm.php
		$apiKey = Config::get('mygcm.apiKey');

		// $all_client = Gcm::select('gcm_id')->get()->toArray();
		$all_client = DB::table('gcms')->lists('regid');
		// return Response::json($all_client);

		$url = 'https://android.googleapis.com/gcm/send';

		// 要發送的訊息內容
	    // 例如我要發送 message, campaigndate, title, description 四樣資訊
	    // 就將這 4 個組成陣列
	    // 您可依您自己的需求修改
	    $fields = array('registration_ids'  => $all_client,
	                    'data'              => array( 'message' => $message ),
	                    'delay_while_idle' => true,
	                    // 'time_to_live' => 30
	                );
	 
	    $headers = array('Content-Type: application/json',
	                     'Authorization: key='.$apiKey
	                    );
	
	    // Open connection
	    $ch = curl_init();
	    // Set the url, number of POST vars, POST data
	    curl_setopt( $ch, CURLOPT_URL, $url );
	    curl_setopt( $ch, CURLOPT_POST, true );
	    curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
	    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
	 
	    // 送出 post, 並接收回應, 存入 $result
	    $result = curl_exec($ch);
		// unset($all_client);

		// return $result;
	     
	    // 由回傳結果, 取得已解除安裝的 regID
	    // 自資料庫中刪除
	    $aGCMresult = json_decode($result,true);
	    $aUnregID = $aGCMresult['results'];
	    $unregcnt = count($aUnregID);
	    for($i=0;$i<$unregcnt;$i++)
	    {
	        $aErr = $aUnregID[$i];
	        if(isset($aErr['error']))
	        {
	            $gcm_to_del = Gcm::where('regid', $all_client[$i])->delete();
	            // $gcm_to_del->delete();
	    //         // $sqlTodel = "DELETE FROM gcmclient
	    //         //                  WHERE gcm_id='".$aRegID[$i]."' ";
	    //         // $pdo->query($sqlTodel);
	        }
	    }
	 
	    // Close connection
	    curl_close($ch);
	    // GCM end -------
	    unset($all_client);

		// return Response::json([$all_client[1]]);
		// return $result;
		return json_decode($result,true);
		// return (isset($aErr['error']))?$aErr['error']:"no data";


	}

}
