@extends('layouts.admin')

@section('title', __('Create A New Category'))

@section('breadcrumb')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">{{ __('Categories') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Create') }}</li>
</ol>
@endsection

@section('content')

    <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">

        @csrf

        @include('admin.categories._form', [
            'button' => 'Add'
        ])
        
    </form>

@endsection