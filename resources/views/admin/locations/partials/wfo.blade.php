@php($items = $locations->where('type', 'wfo'))
@if($items->isEmpty())
    <tr>
        <td colspan="5" class="px-8 py-8 text-center text-gray-500">Belum ada lokasi WFO.</td>
    </tr>
@else
    @foreach($items as $location)
        <tr class="hover:bg-sky-50 transition-colors duration-200 group">
            <td class="px-4 py-6">
                <input type="checkbox" name="ids[]" value="{{ $location->id }}" class="row-select w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
            </td>
            <td class="px-8 py-6 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-base font-semibold text-gray-700">{{ $location->name }}</div>
                        <div class="text-xs mt-1">
                            <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-medium">WFO</span>
                            @if($location->is_active)
                                <span class="ml-2 px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-[11px]">Aktif</span>
                            @else
                                <span class="ml-2 px-2 py-0.5 rounded-full bg-gray-200 text-gray-700 text-[11px]">Nonaktif</span>
                            @endif
                        </div>
                    </div>
                </div>
            </td>
            <td class="px-8 py-6 whitespace-nowrap">
                <div class="text-sm text-gray-900">
                    <div class="font-medium">{{ number_format($location->latitude, 6) }}</div>
                    <div class="text-xs text-gray-500">{{ number_format($location->longitude, 6) }}</div>
                </div>
            </td>
            <td class="px-8 py-6 whitespace-nowrap">
                <div class="flex items-center space-x-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-sky-100 text-sky-800">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                            <circle cx="12" cy="12" r="10"></circle>
                            <circle cx="12" cy="12" r="6"></circle>
                            <circle cx="12" cy="12" r="2"></circle>
                        </svg>
                        {{ $location->radius }}m
                    </span>
                    @if($location->radius <= 100)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Ketat</span>
                    @elseif($location->radius <= 500)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Sedang</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">Luas</span>
                    @endif
                </div>
            </td>
            <td class="px-8 py-6 whitespace-nowrap text-left">
                <div class="flex items-center justify-start space-x-3">
                    <a href="{{ route('admin.locations.edit', $location) }}" class="inline-flex items-center px-4 py-2 bg-sky-100 hover:bg-sky-200 text-sky-700 font-semibold text-sm rounded-lg transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        Edit
                    </a>
                    <form action="{{ route('admin.locations.toggle-active', $location) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 {{ $location->is_active ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }} font-semibold text-sm rounded-lg transition-all duration-200">
                            @if($location->is_active)
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"/></svg>
                                Nonaktifkan
                            @else
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"/></svg>
                                Aktifkan
                            @endif
                        </button>
                    </form>
                    <form action="{{ route('admin.locations.destroy', $location) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus lokasi ini?')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 font-semibold text-sm rounded-lg transition-all duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" x2="10" y1="11" y2="17"></line><line x1="14" x2="14" y1="11" y2="17"></line></svg>
                            Hapus
                        </button>
                    </form>
                </div>
            </td>
        </tr>
    @endforeach
@endif
