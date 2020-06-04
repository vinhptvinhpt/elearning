@extends('layouts.dashboard')
@section('content')
    <div class="row">
        <div class="col">
            <nav class="breadcrumb" aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent px-0">
                    <li class="breadcrumb-item"><a href="{{ __('language') }}/dashboard">{{ __('dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('danh_sach_email_template') }}</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <email_template_index></email_template_index>
        </div>
    </div>
@endsection

