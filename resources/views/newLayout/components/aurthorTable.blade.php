<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header pb-0">
                <h6>Authors table</h6>
                <div class="row pb-3">
                    <div class="col-8">
                        <div class="input-group">
                            <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                            <input type="text" class="form-control search-user" id="search-user" placeholder="Search User">
                        </div>
                    </div>
                    <div class="col-4">
                        <button  class="btn  btn-primary mb-0" style="background-color:#FF9800;">
                            <a href="#popup3" style="color:white;" onclick="add_class('show');">ADD USER</a>
                        </button>
                        <button  class="btn  btn-primary mb-0" style="background-color:#00ff00;"  > 
                            <a href="#popup33" style="color:white;">Yesterday</a></button>
                        <div id="popup33" class="overlay" style="z-index: 99999;">
                            <div class="popup">
                                <h2>Add History</h2>
                                <a class="close" href="#">&times;</a>
                                <div class="content ">
                                    <form action="{{route('addHistory')}}" method="post">
                                    @csrf
                                    <input type="hidden" name="yesterday" value="1">
                                    <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="cars">User: Full Name [ Facebook Name ]</label>
                                        <select name="id" id="form-game-change" class="form-control" required>
                                            @if (isset($form_games) && !empty($form_games))
                                                @foreach($form_games as $a => $num)
                                                <option value="{{$num['form_id']}}" data-id="{{$num['account_id']}}">{{$num['form']['full_name']}}  [{{!empty($num['game_id'])?$num['game_id']:''}}]</option>
                                                @endforeach
                                            @else
                                                <option disabled>No Users</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="amount_loaded">Amount</label>
                                        <input type="number" name="amount_loaded" placeholder="Amount" class="form-control" required>
                                    </div>
                                    {{-- <div class="form-group col-md-6">
                                        <label for="date">Date</label>
                                        <input type="date" name="date" placeholder="Date" class="form-control" required>
                                    </div> --}}
                                    <div class="form-group col-md-6">
                                        <label for="type">Select Game</label>
                                        <select name="account" id="game-id-monthly" class="form-control" required>
                                            @if (isset($games) && !empty($games))
                                                @foreach($games as $a => $num)
                                                <option class="game-id-option" value="{{$num['id']}}">{{$num['name']}}</option>
                                                @endforeach
                                            @else
                                                <option disabled>No Users</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="type">Select Type</label>
                                        <select name="type" id="type" class="form-control" required>
                                            <option value="load">Load</option>
                                            <option value="redeem">Redeem</option>
                                            <option value="refer">Bonus</option>
                                            <option value="tip">Tip</option>
                                        </select>
                                    </div>
                                    <button type="submit"  class="btn  btn-primary mb-0" style="background-color:#FF9800;"  >ADD</button>
                                
                                    </div>
                                </form>
                                </div>
                            </div>
                        </div>
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
                <div id="popup3" class="overlay">
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
