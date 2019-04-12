@extends('layouts.master')
@section('content')
    <section class="row">
        <section class="col-sm-12 mt-4 mb-4">
            <section class="card flat">
                <header class="card-header">
                    <div class="btn-group" role="group">
                        @foreach($sheet_names as $sheet_name)
                            <a href="{{ route('spreadsheet.edit', [
                        'spreadsheet' => $spreadsheet_id,
                        'sheet' => $sheet_name,
                        ]) }}"
                               class="text-white btn-sm pointer btn {{ $sheet === $sheet_name  ? 'btn-primary' : 'btn-secondary' }} flat">{{ $sheet_name }}</a>
                        @endforeach
                    </div>
                    <a href="javascript:void(0)"
                       data-sheet="{{ array_flip($sheet_names)[$sheet] }}"
                       data-spreadsheet="{{ $spreadsheet_id }}"
                       data-name="{{ $sheet }}"
                       class="float-right btn btn-danger flat btn-sm remove-sheet">
                        <i class="fas  fa-trash"></i> Delete `{{ $sheet }}`
                    </a>
                    <a href="javascript:void(0)" data-spreadsheet="{{ $spreadsheet_id }}"
                       class="float-right btn btn-primary flat btn-sm create-sheet mr-3">
                        <i class="fas fa-plus"></i> Create Sheet
                    </a>
                    <span class="clearfix"></span>
                </header>
                <article class="card-body">
                    @include('flash::message')

                    @include('sheets._grid')

                    @if(!empty($records))
                        <section>
                            <form method="POST"
                                  action="{{ route('spreadsheet.update',['spreadsheet_id' => $spreadsheet_id, 'sheet' => $sheet ]) }}">
                                @csrf
                                <div class="custom-table">
                                    <table>
                                        @foreach($records as $row_key => $rows)
                                            <tr>
                                                @foreach($rows as $record_key => $record)
                                                    <td><input type="text"
                                                               name="records[{{ $row_key }}][{{ $record_key }}]"
                                                               value="{{ $record }}" class="form-control"
                                                               style="width:200px!important;"></td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                                <div class="form-group ml-5 mt-5 mb-5">
                                    <button class="btn btn-primary flat" type="submit">Update</button>
                                </div>
                            </form>
                        </section>
                    @endif
                </article>
            </section>
        </section>
    </section>
@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            // create spread sheet
            $('.create-sheet').click(function (e) {
                e.preventDefault();
                self = $(this);
                swal.fire({
                    title: 'Enter Sheet Name',
                    input: 'text',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Create',
                    showLoaderOnConfirm: true,
                    preConfirm: (sheet) => {
                        let response = false;
                        $.ajax({
                            type: 'POST',
                            url: '{{ route('sheet.store') }}',
                            data: {_token: '{{ csrf_token() }}', sheet: sheet, spreadsheet: self.data('spreadsheet')},
                            success: function (res) {
                                swal.fire({
                                    type: 'success',
                                    text: res.message,
                                    onClose: function () {
                                        location.reload();
                                    }
                                });
                                response = true;
                            }, error: function (err) {
                                if (err.responseJSON.errors) {
                                    swal.fire({
                                        type: 'error',
                                        text: err.responseJSON.errors.spreadsheet[0]
                                    });
                                    response = false;
                                    return;
                                }
                                swal.fire({
                                    type: 'error',
                                    text: err.responseJSON.error
                                });
                                response = false;
                            }
                        });
                        return response;
                    },
                    allowOutsideClick: () => !swal.isLoading()
                });
            });

            // remove sheet
            $('.remove-sheet').on('click', function (e) {
                e.preventDefault();
                self = $(this);
                let spreadsheet = self.data('spreadsheet');
                swal.fire({
                    title: 'Do you want to Remove the sheet `' + $(this).data('name') + '`?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Remove!',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        let response = false;
                        $.ajax({
                            type: 'DELETE',
                            url: '{{ route('sheet.delete') }}',
                            data: {_token: '{{ csrf_token() }}', sheet: $(this).data('sheet'), spreadsheet: $(this).data('spreadsheet')},
                            success: function (res) {
                                swal.fire({
                                    type: 'success',
                                    text: res.message,
                                    onClose: function () {
                                        self.parent().parent().remove();
                                        if ($('.table tr').length === 0) {
                                            location.href = '/spreadsheet/'+ spreadsheet;
                                        }
                                    }
                                });
                                response = true;
                            }, error: function (err) {
                                swal.fire({
                                    type: 'error',
                                    text: err.responseJSON.error
                                });
                                response = false;
                            }
                        });
                        return response;
                    }
                });

            });
        });
    </script>
@endpush
