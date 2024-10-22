<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h4>Upload form</h4>
        </div>
        <div class="card-body">
            <form>
                <div class="form-group">
                    <label for="file">File</label>
                    <input type="file" class="form-control" id="file">
                </div>
                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
    <div class="card mt-5">
        <div class="card-header">
            <h4>Cluster</h4>
        </div>
        <div class="card-body">
            <form method="post" action="{{ route('calculate') }}">
                @csrf
                <div class="form-group">
                    <label for="k">Masukkan K :</label>
                    <input type="number" class="form-control" id="k" name="k">
                </div>
                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-success">Kalkulasi</button>
                </div>
            </form>

            @foreach($clusters as $members)
                <div class="card mt-4">
                    <div class="card-header">
                        <h4>Cluster - {{ $loop->iteration }}</h4>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama File</th>
                                <th>Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($members as $member)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $member->file->real_name }}</td>
                                    <td><a href="{{ $member->file->file_url }}" class="btn btn-success"><i class="fa fa-download"></i></a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
</body>
</html>
