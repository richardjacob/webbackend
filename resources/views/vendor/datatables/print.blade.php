<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Print Table</title>
        <meta charset="UTF-8">
        <meta name=description content="">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Bootstrap CSS -->
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        <style>
            body {margin: 20px}
            #filter{
                margin-bottom:5px;
            }
            #filter td:first-child{
                padding-right:20px;
            }
        </style>
    </head>
    <body>
        
        @if($filter = printExtraData())@endif
        @foreach($filter as $k => $v)
            @php 
                $i = $i ?? 0 + 1; 
                if($i == 1) echo "<table id='filter'>";
            @endphp
            <tr>
                <td>{{$k}} :</td>
                <td>{{$v}}</td>
            </tr>
            @php 
                $i++; 
                if($i > count($filter)) echo "</table>";
            @endphp
        @endforeach
        


        <table class="table table-bordered table-condensed">
            @foreach($data as $row)
                @if ($row == reset($data)) 
                    <tr>
                        @foreach($row as $key => $value)
                            <th>{!! $key !!}</th>
                        @endforeach
                    </tr>
                @endif
                <tr>
                    @foreach($row as $key => $value)
                        @if (is_string($value) || trim($value)==='' || is_numeric($value))
                            <td>{!! $value !!}</td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </table>
    </body>
</html>
