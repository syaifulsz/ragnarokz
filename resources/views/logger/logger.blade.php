@inject('carbon', '\Carbon\Carbon')

@extends('layouts.master')

@section('content')
    <div class="container">
        @if (@$logs)
            <div class="well table-container">
                @if ($logs === null)
                <div>Log file >50M, please download it.</div>
                @else
                    <table id="table-log" class="table table-striped">
                        <thead><tr><th>Logs</th></tr></thead>
                        <tbody>
                            @foreach($logs as $key => $log)
                                <tr>
                                    <td class="text js-stack-container" data-stack="#stack{{{$key}}}" data-sort="{{ $log['date'] }}" style="cursor: pointer;">
                                        <small class="text-muted" data-toggle="tooltip" data-placement="top" title="{{ $carbon->parse($log['date'])->toRssString() }}">{{ $carbon->parse($log['date'])->diffForHumans() }}</small><br />
                                        <div class="" style="font-size: 11px;">
                                            <strong>{{{$log['text']}}}</strong>
                                            <strong>@if (isset($log['in_file'])) <br />{{{$log['in_file']}}}@endif</strong>
                                            @if ($log['stack']) <div class="stack" id="stack{{{$key}}}" style="display: none; white-space: pre-wrap;">{{{ trim($log['stack']) }}}</div>@endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
            <div>
                @if (@$current_file)
                    <a class="btn btn-default" href="?dl={{ base64_encode($current_file) }}"><span class="glyphicon glyphicon-download-alt"></span> Download file</a>
                    <a class="btn btn-default" id="delete-log" href="?del={{ base64_encode($current_file) }}"><span class="glyphicon glyphicon-trash"></span> Delete file</a>
                @endif
            </div>
        @else
            <div class="alert alert-success">
                <h4>Good job!</h4>
                <p>You have no error reported!</p>
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/plug-ins/9dcbecd42ad/integration/bootstrap/3/dataTables.bootstrap.css">
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/1.10.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/9dcbecd42ad/integration/bootstrap/3/dataTables.bootstrap.js"></script>
    <script>
    $(document).ready(function() {
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
        $('#table-log').DataTable({
            "order": [ 0, 'desc' ],
            "stateSave": true,
            "stateSaveCallback": function (settings, data) {
                window.localStorage.setItem("datatable", JSON.stringify(data));
            },
            "stateLoadCallback": function (settings) {
                var data = JSON.parse(window.localStorage.getItem("datatable"));
                if (data) data.start = 0;
                return data;
            }
        });
        $(document).on('click', '.js-stack-container', function() {
            $($(this).data('stack')).toggle();
        });
        $('#delete-log').click(function() {
            return confirm('Are you sure?');
        });
    });
    </script>
@endpush