@if(empty($records))
    <!-- generate grid -->
    <form action="" method="GET" class="form-inline mb-2">
        <div class="form-group mr-2"><input type="number" min="1" name="row_count"
                                            placeholder="No.of Rows"
                                            value="{{ request('row_count', 0) }}" required></div>
        <div class="form-group mr-2"><input type="number" min="1" name="col_count"
                                            value="{{ request('col_count',0) }}" required
                                            placeholder="Now of Cols"></div>
        <div class="form-group mr-2">
            <button type="submit" class="btn btn-sm btn-outline-secondary flat">Generate Grid
            </button>
        </div>
    </form>
    <!-- ./ generate grid -->
@endif
@if(request('row_count') && request('col_count'))
    <form method="POST"
          action="{{ route('spreadsheet.update',['spreadsheet_id' => $spreadsheet_id, 'sheet' => $sheet ]) }}">
        @csrf
        <div class="custom-table">
            <table>
                @for($i=0;$i< intval(request('row_count'));$i++)
                    <tr>
                        @for($j=0;$j<intval(request('col_count'));$j++)
                            <td>
                                <input class="form-control" type="text"
                                       name="records[{{ $i }}][{{ $j }}]"
                                       value="" style="width:200px!important;">
                            </td>
                        @endfor
                    </tr>
                @endfor
            </table>
        </div>
        <div class="form-group ml-5 mt-5 mb-5">
            <button class="btn btn-primary flat" type="submit">Update</button>
        </div>
    </form>

@endif
