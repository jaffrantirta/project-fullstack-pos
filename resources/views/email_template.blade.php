<html>
    <head>

    </head>
    <body>
        <div>
            <h3>{{ $mail['title'] }}</h3><br>
        </div>
        <div>
            <strong>{{ $mail['opening'] }}</strong><br><br>
            <strong><b>{{ $mail['content'] }}</b></strong><br><br>
        </div>
        <div>
            <strong><i>{{ $mail['closing'] }}</i></strong><br>
            <small>{{ $mail['closing_content'] }}</small>
        </div>


    </body>
</html>