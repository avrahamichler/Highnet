<?php

namespace api\components;



class GcmPush
{
    const API_KEY = "AIzaSyAM-cnUjns0dMaVDsrg22lvyKRt-kXNOTo";
    const GCM_URL = 'https://android.googleapis.com/gcm/send';

    function GcmPush()
    {

    }

    public function pushToOne($registration_gcm, $msgArr)
    {
        $arr = array($registration_gcm);
        return $this->executePush($arr, $msgArr);
    }

    public function pushToSome($registration_gcm, $msgArr)
    {
        if (is_array($registration_gcm))

        return $this->executePush($registration_gcm, $msgArr);
    }

    private function executePush($regArr, $msgArr)
    {
        //Prepare variables
        $fields = array(
            'registration_ids' => $regArr,
            'data' => $msgArr,
        );

        $headers = array(
            'Authorization: key=' . GcmPush::API_KEY,
            'Content-Type: application/json'
        );

        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, GcmPush::GCM_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        // Execute post
        $result = curl_exec($ch);

        if ( curl_errno( $ch ) )
        {
            echo 'GCM error: ' . curl_error( $ch );
        }

        // Close connection
        curl_close($ch);
        //echo $result ;
        //ob_flush();
        //flush();
        //Return push response as array
        return json_decode($result);
    }


}

?>