@extends('layouts.admin')

@section('title', 'Detalle de Rifa')

@section('content')
  {{-- Header destacado --}}
  @include('admin.rifas.partials._header', ['rifa' => $rifa])

  {{-- Ganador principal --}}
  @include('admin.rifas.partials._ganador', ['rifa' => $rifa])

  {{-- Premios especiales --}}
  @include('admin.rifas.partials._premios', ['rifa' => $rifa])



@endsection
