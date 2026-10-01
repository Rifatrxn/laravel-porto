@extends('layouts.portfolio')

@section('title', '')

@section('content')
    @include('portfolio.partials.hero')
    @include('portfolio.partials.stats')

    @if (setting_on('section_about', true) && $profile->about)
        @include('portfolio.partials.about')
    @endif

    @if (setting_on('section_services', true) && $services->isNotEmpty())
        @include('portfolio.partials.services')
    @endif

    @if (setting_on('section_skills', true) && $skills->isNotEmpty())
        @include('portfolio.partials.skills')
    @endif

    @if (setting_on('section_projects', true) && $projects->isNotEmpty())
        @include('portfolio.partials.projects')
    @endif

    @if (setting_on('section_experience', true) && ($experiences->isNotEmpty() || $educations->isNotEmpty()))
        @include('portfolio.partials.experience')
    @endif

    @if (setting_on('section_testimonials', true) && $testimonials->isNotEmpty())
        @include('portfolio.partials.testimonials')
    @endif

    @if (setting_on('section_contact', true))
        @include('portfolio.partials.contact')
    @endif
@endsection
