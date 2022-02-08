@if(count($posts))
    @foreach($users as $user)
        @include('elements.search.users-list-element',['user'=>$user])
    @endforeach
@else
    <h5 class="text-center mb-2 mt-2">{{__('No users were found')}}</h5>
@endif
