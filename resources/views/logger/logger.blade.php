@inject('carbon', '\Carbon\Carbon')

@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="well table-container">
            @if ($logs === null)
            <div>Log file >50M, please download it.</div>
            @else
                <table id="table-log" class="table table-striped">
                    <thead><tr><th>Level</th><th>Date</th><th>Content</th></tr></thead>
                    <tbody>
                        @foreach($logs as $key => $log)
                            <tr>
                                <td class="text-{{{$log['level_class']}}}">{{$log['level']}}</td>
                                <td class="date">
                                    <span data-toggle="tooltip" data-placement="top" title="{{ $carbon->parse($log['date'])->toRssString() }}">{{ $carbon->parse($log['date'])->diffForHumans() }}</span>
                                </td>
                                <td class="text">
                                    @if ($log['stack']) <a class="pull-right expand btn btn-default btn-xs" data-display="stack{{{$key}}}"><span class="glyphicon glyphicon-search"></span></a>@endif
                                    {{{$log['text']}}}
                                    @if (isset($log['in_file'])) <br />{{{$log['in_file']}}}@endif
                                    @if ($log['stack']) <div class="stack" id="stack{{{$key}}}" style="display: none; white-space: pre-wrap;">{{{ trim($log['stack']) }}}</div>@endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        <div>
            <a class="btn btn-default" href="?dl={{ base64_encode($current_file) }}"><span class="glyphicon glyphicon-download-alt"></span> Download file</a>
            <a class="btn btn-default" id="delete-log" href="?del={{ base64_encode($current_file) }}"><span class="glyphicon glyphicon-trash"></span> Delete file</a>
        </div>
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
            "order": [ 1, 'desc' ],
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
        $('.table-container').on('click', '.expand', function(){
            $('#' + $(this).data('display')).toggle();
        });
        $('#delete-log').click(function(){
            return confirm('Are you sure?');
        });
    });
    </script>
@endpush