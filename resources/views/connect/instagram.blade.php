@extends('layouts.web')

@section('content')

    <section class="bg-half d-table w-100" style="background-image:url({{ $header }});padding:36px">
        <div class="bg-overlay"></div>
    </section>

    <section class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    @if ($errors->has('instagram_id'))
                        <div class="alert alert-danger">Has been an error saving your data. Please try again later.</div>
                    @endif
                    @if ($accounts->count())
                        <form action="/connect/instagram" method="POST" class="my-5">
                            @csrf
                            @foreach ($accounts as $account)
                                <div class="card shadow rounded border-0 overflow-hidden mb-3">
                                    <div class="row no-gutters">
                                        <div class="col-2 p-4">
                                            <img src="{{ $account->picture->data->url }}" class="img-fluid" alt="{{ $account->name }}">
                                        </div>
                                        <div class="col-10">
                                            <div class="card-body pl-0">
                                                <h5 class="card-title">{{ $account->name }}</h5>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="facebook-{{ $account->id }}" name="facebook_id" value="{{ $account->id }}" class="custom-control-input">
                                                    <label class="custom-control-label" for="facebook-{{ $account->id }}">Connect with Facebook page</a></label>
                                                </div>
                                                @if (property_exists($account, 'connected_instagram_account'))
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="instagram-{{ $account->id }}" name="instagram_id" value="{{ $account->connected_instagram_account->id }}" class="custom-control-input">
                                                        <label class="custom-control-label" for="instagram-{{ $account->id }}">Connect with <a href="https://instagram.com/{{ $account->connected_instagram_account->username }}" target="_blank" rel="noreferrer nofollow">{{ '@' . $account->connected_instagram_account->username }}</a></label>
                                                    </div>
                                                @else
                                                    <div class="alert alert-warning small mt-2 mb-0">There is no professional Instagram account linked to this page.</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <button type="submit" class="btn btn-primary btn-block btn-lg"><span class="fa fa-link"></span> Connect</button>
                        </form>
                    @else
                        <div class="alert alert-warning">No Instagram accounts found.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection