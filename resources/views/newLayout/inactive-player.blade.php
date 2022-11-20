@extends('newLayout.layouts.newLayout')

@section('title')
    InActive Players
@endsection
@section('content')
    @php
        use Carbon\Carbon;
    @endphp
    <style>
        .count-row {
            font-weight: 700;
        }

        .select2 {
            width: 100% !important;
        }


        .overlay {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.7);
            transition: opacity 500ms;
            visibility: hidden;
            opacity: 0;
            z-index: 9999;
        }

        .overlay:target {
            visibility: visible;
            opacity: 1;
        }


        .popup {
            margin: 70px auto;
            padding: 20px;
            background: #fff;
            border-radius: 5px;
            width: 50%;
            position: relative;
            transition: all 5s ease-in-out;



        }

        .popup h2 {
            margin-top: 0;
            color: #333;
            font-family: Tahoma, Arial, sans-serif;

        }

        .popup .close {
            position: absolute;
            top: 20px;
            right: 30px;
            transition: all 200ms;
            font-size: 30px;
            font-weight: bold;
            text-decoration: none;
            color: #333;

        }

        .popup .close:hover {
            color: #FF9800;
        }

        .popup .content {
            max-height: calc(100vh - 210px);
            overflow-y: auto;
        }

        @media screen and (max-width: 700px) {
            .box {
                width: 70%;
            }

            .popup {
                width: 70%;
            }
        }
    </style>
    <div class="row justify-content-center">
        <div class="col-12 card">
            <div class="card-body">
                <select class="form-control" name="" id="inactive-players-select">
                    <option value="7" {{ isset($days) && $days == 7 ? 'selected' : '' }}>Inactive since 7 days</option>
                    <option value="10" {{ isset($days) && $days == 10 ? 'selected' : '' }}>Inactive since 10 days
                    </option>
                    <option value="20" {{ isset($days) && $days == 20 ? 'selected' : '' }}>Inactive since 20 days
                    </option>
                </select>
            </div>
            <button class="btn  btn-primary mb-0" style="background-color:#FF9800;">
                <a href="#popup3" style="color:white;">Send Mail</a></button>
            <div id="popup3" class="overlay" style="z-index: 9;">
                <div class="popup">
                    <h2>Send Mail</h2>
                    <a class="close" href="#">&times;</a>
                    <div class="content ">
                        <form action="{{ route('send-message-inactive') }}" method="post">
                            @csrf
                            <input name="id" type="hidden" value="0" class="form-id">
                            <input name="days" type="hidden" value="{{ $days }}">
                            <div class="row">
                                <div class="form-group">
                                    <label for="cars">Message</label>
                                    <textarea name="message" id="cars" class="form-control" placeholder="write your message"></textarea>

                                </div>
                                <button type="submit" class="btn  btn-primary mb-0"
                                    style="background-color:#FF9800;">Send</button>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <p>
                This Data is of {{ $days }} days prior.
            </p>
        </div>
        <div class="col-md-12 card">
            <div class="card-body">
                {{-- <div class="row justify-content-between">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target=".bd-example-modal-lg1">Restore</button>

            </div> --}}
            </div>
            <div class="table-responsive p-4" style="overflow-x:auto;">
                <!--id="datatable-crud"-->
                    <table class="table datatable" style="font-size:14px">
                        <thead>
                            <tr>
                                <th style="width: 26.328100000000006px!important;">No</th>
                                {{-- <th class="big-col">Action</th> --}}
                                <th class="cus-width">Full Name</th>
                                <th class="cus-width">Status</th>
                                <th class="cus-width">Note</th>
                                @if (Auth::user()->role == 'admin')
                                    <th class="cus-width">Number</th>
                                    <th class="cus-width">Email</th>
                                @endif
                                <th class="cus-width">Fb ID</th>
                                {{-- <th class="cus-width">Game ID</th> --}}
                                <th style="width: 56.3125px!important;text-align:center;!important">State</th>
                                {{-- <th class="cus-width">Ref Id</th> --}}
                                <th class="cus-width" style="width: 56.3125px!important;text-align:center;!important">Months
                                </th>
                                <th class="cus-width">Next-Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $count = 1;
                            @endphp
                            @foreach ($forms as $num)
                                <tr class="tr-{{ $num->id }}">
                                    <td class="count-row">{{ $count++ }}</td>
                                    <td class="td-full_name-{{ $num->id }}">{{ ucwords($num->full_name) }}</td>
                                    <td class="td-full_name-{{ $num->id }}">
                                        {{-- select2 form-control--}}
                                        <select id="status-select-{{ $num->id }}" class="activity-status" data-id="{{ $num->id }}">
                                            <option>Select Status</option>
                                            @forelse ($activity_status as $status)
                                                <option class="w-100" value="{{ $status->id }}"
                                                    @if ($num->status_id == $status->id) selected @endif>
                                                    {{ ucfirst($status->status) }}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </td>
                                    <td class="class td-note-{{ $num->id }}" data-id="{{ $num->id }}">
                                        {{ $num->note }}</td>
                                    @if (Auth::user()->role == 'admin')
                                        <td class="td-game_id-{{ $num->id }}">{{ $num->number }}</td>
                                        <td class="td-game_id-{{ $num->id }}">{{ $num->email }}</td>
                                    @endif
                                    <td class="td-facebook_name-{{ $num->id }}">{{ $num->facebook_name }}</td>
                                    {{-- <td class="td-game_id-{{$num->id}}">{{($num->game_id)}}</td> --}}
                                    <td class="td-mail-{{ $num->id }}"
                                        style="width: 56.3125px!important;text-align:center;!important">
                                        {{ ucwords($num->mail) }}</td>
                                    {{-- <td class="td-r_id-{{$num->id}}">{{($num->r_id)}}</td> --}}
                                    <td class="td-count-{{ $num->id }}"
                                        style="width: 56.3125px!important;text-align:center;!important">{{ $num->count }}
                                    </td>

                                    <td class="td-intervals-{{ $num->id }}">
                                        {{-- {{(date_format(date($num->intervals), 'M d,Y H:i:s'))}} --}}
                                        {{ date('d M,Y', strtotime($num->intervals)) }}
                                        {{-- {{Carbon::parse('Y-m-d', $num->intervals)->format('M d Y H:i:s')}} --}}
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
            </div>

        </div>
    </div>
    </div>
    <div style="top: -200px;" class="modal fade bd-example-modal-lg editFormModal" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: {{ isset($color) ? $color->color : 'purple' }}">
                    <h5 class="modal-title" id="exampleModalLabel" style="color: white">Edit User</h5>
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-cross"></i> </button> --}}
                </div>
                <div class="modal-body editFormModalBody">
                    <div class="appendHere">

                    </div>
                </div>
                {{-- <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div> --}}
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).on('change', '#inactive-players-select', function() {
            // console.log($(this).val());
            window.location.replace('/inactive-players/' + $(this).val());
        });
        $(document).on("change", ".activity-status", function(event) {
            event.preventDefault();
            var id = $(this).attr('data-id');
            var status = $(this).val();
            var url = window.location.origin + '/change-activity-status';
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    'id': id,
                    'status': status,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log(response);
                    if (response.status == 'ok') {
                        toastr.success('Success', response.message);
                    }
                },
                error: function(e) {
                    if (e.responseJSON.message) {
                        toastr.error('Error', e.responseJSON.message);
                    } else {
                        toastr.error('Error', 'Something went wrong while processing your request.')
                    }
                }
            });
        });
    </script>
@endsection
