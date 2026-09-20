@extends('layouts.app')

@section('title', 'PRISMA UHN - Portal Riset, Inovasi, Pengabdian & Apresiasi Universitas Harkat Negeri')

@section('content')
    <!-- Navbar Header -->
    @include('components.navbar')

    <main class="flex-grow">
        <!-- Hero Banner Section -->
        @include('components.hero')

        <!-- Statistics Banner Counter -->
        @include('components.stats')

        <!-- 4 Utama Pilar Program BIMA-Style -->
        @include('components.pillars')

        <!-- Interactive Schemes Explorer Filter -->
        @include('components.schemes')

        <!-- Bank Publikasi & Riset Dosen Terbuka (Tanpa Login) -->
        @include('components.public-publications')

        <!-- Wizard 6-Langkah Form Preview -->
        @include('components.wizard-preview')

        <!-- Interactive Tools: SINTA Checker & QR Validator -->
        @include('components.tools-preview')

        <!-- National Ecosystem Integrations -->
        @include('components.integrations')

        <!-- 4 Fakultas & 22 Program Studi Directory -->
        @include('components.faculties')

        <!-- Business Process Timeline -->
        @include('components.timeline')

        <!-- Call To Action -->
        @include('components.cta')
    </main>

    <!-- Footer -->
    @include('components.footer')
@endsection

