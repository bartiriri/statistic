LOG_HITS = (function(){
    const logHits = ($session,$ip,$browse,$url) =>{
        $.post("api/Log.php",
            {
                session: $session,
                ip: $ip,
                browse: $browse,
                url: $url
            },
            function(data, status){
                alert("Data: " + data + "\nStatus: " + status);
            });

    }

    return{
        'logHits': logHits
    }
})()