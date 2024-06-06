@extends('backend.layouts.master')
@section('content')
    <!-- Main content -->
    <div class="mt-4">
        <div class="d-flex justify-content-between mb-4">
            <div class="border-bottom border-primary">
                <h2>
                    {{ $page_title }}
                </h2>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div id="example1_wrapper" class="dataTables_wrapper dt-bootstrap4">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-12">
                        <div class="box box-info" style="padding:5px;">
                            <div class="box-header with-border">

                                <div class="pull-right box-tools">
                                    <a class="btn btn-primary btn-sm">

                                        <div class="form-group col-lg-4 col-sm-4">
                                            <label for="resignation_letter">Resignation Letter</label>
                                            <input type="text" name="resignation_letter"
                                                value="{{ old('resignation_letter') }}" class="form-control"
                                                id="resignation_letter" placeholder="Enter resignation_letter" required>
                                            @error('resignation_letter')
                                                <strong class="text-danger">{{ $message }}</strong>
                                            @enderror
                                        </div>
                                        <div class="form-group col-lg-3 col-sm-3 mt-2">
                                            <label for="note">Note :</label>
                                            <textarea name="note" class="form-control" id="note" placeholder="Note.." rows="15" cols="50">
                                                        {{ old('note') }}
                                                    </textarea>

                                            @error('note')
                                                <strong class="text-danger">{{ $message }}</strong>
                                            @enderror
                                        </div>