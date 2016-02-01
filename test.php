<?php
function getdb_FAILS() {
    return pg_connect("host=94.188.161.142 port=5432 dbname=sns user=snspp password=highnet") or die('connection failed');
}
if(getdb_FAILS()){
    echo "good";
}

?>