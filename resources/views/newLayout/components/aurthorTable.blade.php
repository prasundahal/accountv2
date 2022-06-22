@php
// dd($_GET);
    if(isset($_GET['date']) && !empty($_GET['date'])){
        session()->put('tableDate',$_GET['date']);
    }
@endphp
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header pb-0">
                <div class="">

                    <h6>Authors table</h6>
                    
                    <button  class="btn  btn-primary mb-0" style="background-color:#00ff00;"> 
                        <a href="#popup3" style="color:white;">
                            <span>[ Selected Date : {{((isset($_GET['date']) && !empty($_GET['date']))?session()->get('tableDate'):'')}} ]</span> 
                        </a>
                    </button>
                    <div id="popup3" class="overlay" style="z-index: 9;">
                        <div class="popup">
                            <h2>Select Date</h2>
                            <a class="close" href="#">&times;</a>
                            <div class="content ">
                                <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="tableDate">Select Date</label>
                                    <input type="date" name="tableDate" id="tableDate">
                                </div>
                            </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row pb-3">
                    <div class="col-8">
                        <div class="input-group">
                            <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                            <input type="text" class="form-control search-user" id="search-user" placeholder="Search User">
                        </div>
                    </div>
                    <div class="col-4">
                        <button  class="btn  btn-primary mb-0" style="background-color:#FF9800;">
                            <a href="#popup33" style="color:white;" onclick="add_class('show');">ADD USER</a>
                        </button>
                        <button  class="btn  btn-primary mb-0 undo-transaction" style="background-color:#1100ff;">
                            <a href="#popup7" style="color:white;">Undo</a>
                        </button>
                        <div id="popup7" class="overlay" style="max-width:100%; width:100%;">
                            <div class="popup">
                                <h2>Undo Transaction</h2>
                                <a class="close" href="#">&times;</a>
                                <div class="content ">
                                    <div class="row" style="padding-top:20px;">
                                        <div class="col-12">
                                            <div class="card mb-4">
                                                <div class="card-body px-0 pt-0 pb-2">
                                                    <div class="table-responsive p-0">
                                                        <table class="table table-responsive align-items-center mb-0">
                                                            <thead class="sticky" >
                                                                <tr>
                                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">FB Name</th>
                                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Game</th>
                                                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Game Id</th>
                                                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Amoount</th>
                                                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Type</th>
                                                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Created By</th>
                                                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action by</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody  style="text-align: center!important;" class="undo-history-body">
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div >
                            </div >
                        </div >
                    </div>
                </div>
                <div id="popup33" class="overlay">
                    <div class="popup">
                        <h2>User</h2>
                        <a class="close" href="#">&times;</a>
                        <div class="content ">
                            <label for="cars">User: Full Name [ Facebook Name ]</label>
                            <form action="{{route('addUser')}}" method="post">
                                @csrf
                                <input type="hidden" name="account_id" value="{{$activeGame['id']}}">
                                <select name="id" id="id" class="select2" required>
                                    @if (isset($forms) && !empty($forms))
                                    @foreach($forms as $a => $num)
                                    <option value="{{$num['id']}}">{{$num['full_name']}}  [{{(!empty($num['facebook_name'])?$num['facebook_name']:'empty')}}]</option>
                                    @endforeach
                                    @else
                                    <option disabled>No Users</option>
                                    @endif
                                </select>
                                <br>
                                <label for="cars">Game Id:</label>
                                <input class="form-control" type="text" name="game_id" id="game_id" required>
                                {{-- <input class="form-control" type="text" value=""> --}}
                                <br>
                                <button type="submit"  class="btn  btn-primary mb-0" style="background-color:#FF9800;"  >ADD</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body px-0 pt-0 pb-2" style="background: #ffff!important">
            <div class="table-responsive p-0 authorTable">
                @include('newLayout.components.listTable')
            </div>
        </div>
    </div>
</div>
@section('script')
<script>
    $(document).on('change','#tableDate',function(){
        // console.log($(this).val());
        window.location.replace('/account/table?date='+$(this).val());
    });
</script>
@endsection
