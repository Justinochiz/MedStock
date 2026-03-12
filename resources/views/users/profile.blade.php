@extends('layouts.base')

@section('body')
    <div class="container">
        @include('layouts.flash-messages')
        {!! Form::open(['route' => 'customers.store']) !!}
        {!! Form::label('desc', 'title', ['class' => 'form-label']) !!}
        {!! Form::text('title', null, ['class' => 'form-control', 'id' => 'desc']) !!}

        {!! Form::label('lname', 'last name', ['class' => 'form-label']) !!}
        {!! Form::text('lname', null, ['class' => 'form-control', 'id' => 'lname']) !!}


        {!! Form::label('fname', 'first name', ['class' => 'form-label']) !!}
        {!! Form::text('fname', null, ['class' => 'form-control', 'id' => 'fname']) !!}

        {!! Form::label('address', 'address', ['class' => 'form-label']) !!}
        {!! Form::text('addressline', null, ['class' => 'form-control', 'id' => 'address']) !!}

        {!! Form::label('town', 'town', ['class' => 'form-label']) !!}
        {!! Form::text('town', null, ['class' => 'form-control', 'id' => 'town']) !!}

        {!! Form::label('phone', 'phone number', ['class' => 'form-label']) !!}
        {!! Form::text('phone', null, ['class' => 'form-control', 'id' => 'phone']) !!}

        {!! Form::label('zipcode', 'zip code', ['class' => 'form-label']) !!}
        {!! Form::text('zipcode', null, ['class' => 'form-control', 'id' => 'zipcode']) !!}
        {!! Form::submit('Add item', ['class' => 'btn btn-primary']) !!}
        {!! Form::close() !!}
    </div>
@endsection
