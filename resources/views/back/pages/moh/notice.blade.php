@extends('back.layout.pages-layout')
@section('pagetitle', isset($pageTitle) ? $pageTitle : 'Notice')
@section('content')
<div class="col-md-12">
    <div class="container">
        <h2>Create Notice</h2>
        <form action="{{ route('moh.add-notice') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="content">Content</label>
                <textarea name="content" class="form-control" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label for="notice_type">Notice Type</label>
                <select name="notice_type" class="form-control">
                    <option value="general">General</option>
                    <option value="urgent">Urgent</option>
                    <option value="reminder">Reminder</option>
                </select>
            </div>
            <div class="form-group">
                <label for="target_group">Target Group</label>
                <select name="target_group" class="form-control">
                    <option value="parents">Parents</option>
                    <option value="midwives">Midwives</option>
                    <option value="all">All</option>
                </select>
            </div>
            <div class="form-group">
                <label for="scheduled_at">Scheduled At</label>
                <input type="datetime-local" name="scheduled_at" class="form-control">
            </div>
            <div class="form-group">
                <label for="expires_at">Expires At</label>
                <input type="datetime-local" name="expires_at" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Send Notice</button>
        </form>
    </div>
</div>
@endsection
