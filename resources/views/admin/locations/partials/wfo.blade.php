@php($items = $locations->where('type', 'wfo'))
@if($items->isEmpty())
    <tr>
        <td colspan="5" class="px-8 py-16 text-center">
            <div class="flex flex-col items-center">
                <div class="w-20 h-20 bg-gradient-to-br from-sky-100 to-sky-200 rounded-full flex items-center justify-center mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-sky-400">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Belum ada lokasi WFO</h3>
                <p class="text-gray-600 mb-6 max-w-sm">Mulai dengan menambahkan lokasi WFO untuk check-in karyawan</p>
                <a href="{{ route('admin.locations.create') }}"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 text-white font-bold rounded-xl transition-all duration-200 transform   shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Lokasi WFO
                </a>
            </div>
        </td>
    </tr>
@else
    @foreach($items as $location)
        <tr class="hover:bg-sky-50 transition-colors duration-200 group">
            <td class="px-4 py-6">
                <input type="checkbox" name="ids[]" value="{{ $location->id }}" class="row-select w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
            </td>
            <td class="px-8 py-6 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center mr-4 group-hover:from-sky-200 group-hover:to-sky-300 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-sky-600">
                            <rect width="18" height="18" x="3" y="3" rx="2"/>
                            <path d="M3 9h18"/>
                            <path d="M9 21V9"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-base font-semibold text-gray-900">{{ $location->name }}</div>
                        <div class="flex items-center space-x-2 mt-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                    <rect width="18" height="18" x="3" y="3" rx="2"/>
                                    <path d="M3 9h18"/>
                                </svg>
                                WFO
                            </span>
                            @if($location->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-700">
                                    <span class="w-1.5 h-1.5 bg-gray-500 rounded-full mr-1"></span>
                                    Nonaktif
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </td>
            <td class="px-8 py-6 whitespace-nowrap">
                <div class="flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 mr-2 mt-0.5">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/>
                        <path d="M2 12h20"/>
                    </svg>
                    <div>
                        <div class="text-sm font-semibold text-gray-900">{{ number_format($location->latitude, 6) }}</div>
                        <div class="text-xs text-gray-500">{{ number_format($location->longitude, 6) }}</div>
                    </div>
                </div>
            </td>
            <td class="px-8 py-6 whitespace-nowrap">
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-sky-100 text-sky-800">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1.5">
                            <circle cx="12" cy="12" r="10"></circle>
                            <circle cx="12" cy="12" r="6"></circle>
                            <circle cx="12" cy="12" r="2"></circle>
                        </svg>
                        {{ $location->radius }}m
                    </span>
                    @if($location->radius <= 100)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                            Ketat
                        </span>
                    @elseif($location->radius <= 500)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                <path d="M12 2v20M2 12h20"/>
                            </svg>
                            Sedang
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>
                            </svg>
                            Luas
                        </span>
                    @endif
                </div>
            </td>
            <td class="px-8 py-6 whitespace-nowrap text-left">
                <div class="flex items-center justify-start space-x-2">
                    <a href="{{ route('admin.locations.edit', $location) }}" class="inline-flex items-center px-4 py-2 bg-sky-100 hover:bg-sky-200 text-sky-700 font-semibold text-sm rounded-lg transition-all duration-200  ">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1.5">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        Edit
                    </a>
                    <form action="{{ route('admin.locations.destroy', $location) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus lokasi {{ $location->name }}? Tindakan ini tidak dapat dibatalkan.')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 font-semibold text-sm rounded-lg transition-all duration-200  ">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1.5">
                                <path d="M3 6h18"></path>
                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                <line x1="10" x2="10" y1="11" y2="17"></line>
                                <line x1="14" x2="14" y1="11" y2="17"></line>
                            </svg>
                            Hapus
                        </button>
                    </form>
                </div>
            </td>
        </tr>
    @endforeach
@endif
