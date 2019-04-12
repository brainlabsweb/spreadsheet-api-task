@extends('layouts.master')
@section('content')
    <section class="container mt-4 mb-4">
        <section class="card flat">
            <div class="card-header"># Spreadsheets from your Drive
                <a class="btn btn-primary flat btn-sm float-right create-spreadsheet" href="javascript:void(0)">Create
                    Spreadsheet</a>
                <span class="clearfix"></span>
            </div>
            <article class="card-body">
                @include('flash::message')
                @if(count($spreadsheets))
                    <section class="table-responsive">
                        <table class="table table-bordered">
                            @foreach(array_chunk($spreadsheets, 3) as $chunk)
                                <tr>
                                    @foreach($chunk as $spreadsheet)
                                        <td>
                                            <a class="float-left"
                                               href="{{ route('spreadsheet.edit', $spreadsheet->id) }}">
                                                <i class="far fa fa-file-excel fa-wx text-success"></i> {{ $spreadsheet->name }}
                                            </a>
                                            <a class="float-right remove-spreadsheet pointer btn btn-danger flat btn-sm"
                                               data-name="{{ $spreadsheet->name }}"
                                               data-spreadsheet="{{ $spreadsheet->id }}">
                                                <i class="far fa fa-trash text-white"></i>
                                            </a>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach

                        </table>
                    </section>
                @else
                    <div class="alert alert-info">
                        We Didn't find any spread sheets in your drive
                    </div>
                @endif
            </article>

            <!-- Modal -->
            <div class="modal fade flat" id="create-spreadsheet" tabindex="-1" role="dialog"
                 aria-labelledby="spreadsheet-label" aria-hidden="true">
                <div class="modal-dialog flat" role="document">
                    <div class="modal-content flat">
                        <div class="modal-header">
                            <h5 class="modal-title" id="spreadsheet-label"># Create Spreadsheet</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>
@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            // create spread sheet
            $('.create-spreadsheet').click(function (e) {
                e.preventDefault();
                swal.fire({
                    title: 'Enter Spreadsheet Name',
                    input: 'text',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Create',
                    showLoaderOnConfirm: true,
                    preConfirm: (spreadsheet) => {
                        let response = false;
                        $.ajax({
                            type: 'POST',
                            url: '{{ route('spreadsheet.store') }}',
                            data: {_token: '{{ csrf_token() }}', spreadsheet: spreadsheet},
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
                                if(err.responseJSON.errors) {
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
            // remove spread sheet
            $('.remove-spreadsheet').on('click', function (e) {
                e.preventDefault();
                self = $(this);
                swal.fire({
                    title: 'Do you want to Remove the spread sheet `' + $(this).data('name') + '`?',
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
                            url: '{{ route('spreadsheet.delete') }}',
                            data: {_token: '{{ csrf_token() }}', spreadsheet: $(this).data('spreadsheet')},
                            success: function (res) {
                                swal.fire({
                                    type: 'success',
                                    text: res.message,
                                    onClose: function () {
                                        self.parent().parent().remove();
                                        if ($('.table tr').length === 0) {
                                            location.reload();
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
