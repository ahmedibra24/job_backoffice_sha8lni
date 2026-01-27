<x-app-layout>
    {{--! ============================== HEADER ====================================== --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    {{--! ============================== MAIN CONTENT ====================================== --}}
    <div class="py-12">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
            {{--! overview cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                {{--! total active users card --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-bold text-gray-900">
                        Total Users
                    </div>
                    <div class="mt-2 text-3xl font-semibold text-indigo-600">
                        {{ $analytics['activeUsers'] }}
                    </div>
                    <div class="text-sm  text-gray-500 mt-2">
                        Last 30 days
                    </div>
                </div>
                {{--! total companies/jobs card --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-bold text-gray-900">
                        @if (Auth::user()->role == 'recruiter')
                            Total Jobs
                        @else
                            Total Companies
                        @endif
                    </div>
                    <div class="mt-2 text-3xl font-semibold text-indigo-600">
                       {{ $analytics['totalCompanies'] }}
                    </div>
                    <div class="text-sm  text-gray-500 mt-2">
                        All time
                    </div>

                </div>
                {{--! total job applications card --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-bold text-gray-900">
                        Total Job Applications
                    </div>
                    <div class="mt-2 text-3xl font-semibold text-indigo-600">
                        {{ $analytics['totalJobVacancies'] }}
                    </div>
                    <div class="text-sm  text-gray-500 mt-2">
                        All time
                    </div>
                </div>
            </div>
            {{--! most applied job table --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Most Applied Jobs</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        {{--! table header --}}
                        <thead class="bg-indigo-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Job Title
                                </th>
                                 @if (Auth::user()->role == 'admin')
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Company
                                </th>
                                @endif
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Number of Applications
                                </th>
                            </tr>
                        </thead>
                        {{--! table body --}}
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($analytics['mostAppliedJob'] as $job)
                            <tr class="hover:bg-indigo-50">
                                {{--! job title --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $job->title }}</div>
                                </td>
                                {{--! company name --}}
                                {{--? display company name only for admin --}}
                                @if (Auth::user()->role == 'admin')
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $job->company->name }}</div>
                                </td>
                                @endif
                                {{--! number of applications --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $job->total_applications }}</div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            {{--! conversion rates table --}}
            <div class="bg-white shadow-sm rounded-lg p-6 mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Conversion Rates</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        {{--! table header --}}
                        <thead class="bg-indigo-50">                        
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Job Title
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Views
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Number of Applications
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Conversion Rate
                                </th>
                            </tr>                            
                        </thead>
                        {{--! table body --}}
                        <tbody class="bg-white divide-y divide-gray-200">
                           
                            @foreach ( $analytics['conversionRates'] as $job )
                            <tr class="hover:bg-indigo-50">
                                {{--! job title --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $job->title }}</div>
                                </td>
                                {{--! views --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $job->viewCount }}</div>
                                </td>
                                {{--! number of applications --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $job->total_applications }}</div>
                                </td>
                                {{--! conversion rate --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"> {{ $job->conversionRate }}</div>
                                </td>
                            </tr>
                            @endforeach ()  
                        </tbody>
                    </table>
                </div>
            </div>
    </div>
</x-app-layout>
