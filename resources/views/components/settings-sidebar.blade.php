<!-- Settings Sidebar -->
<div class="col-span-1">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <nav class="space-y-1 p-4">
            <a href="{{ route('staff.settings.general') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition {{ request()->is('staff/settings/general') ? 'bg-blue-50 text-blue-700' : '' }}">
                Generale
            </a>
            <a href="{{ route('staff.delivery-dates.index') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition {{ request()->is('staff/delivery-dates*') ? 'bg-blue-50 text-blue-700' : '' }}">
                Date di consegna
            </a>
            <a href="{{ route('staff.withdraw-dates.index') }}" class="block px-4 py-2 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 transition {{ request()->is('staff/withdraw-dates*') ? 'bg-blue-50 text-blue-700' : '' }}">
                Date di ritiro
            </a>
        </nav>
    </div>
</div>
