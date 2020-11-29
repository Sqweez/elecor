<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<table>
    <thead>
    <tr>
        <th>Имя</th>
        <th>Балансы</th>
    </tr>
    </thead>
    <tbody>
   {{-- @foreach($debts as $debt)
        <tr>
            <td>{{ $debt['name'] }}</td>
            <td>
                <ul>
                    @foreach($debt['connections'] as $connection)
                        <li>
                            {{ $connection['address'] }} {{ $connection['balance'] }}
                        </li>
                    @endforeach
                </ul>
            </td>
        </tr>
    @endforeach--}}
    </tbody>
</table>
</body>
</html>
