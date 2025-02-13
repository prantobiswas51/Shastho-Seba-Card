<x-filament-panels::page>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <div class="">
        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow">
            <div class="mb-4">
                <input type="text" id="search" placeholder="Search..."
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-sky-200">
                            <th class="border border-gray-300 px-4 py-2 text-left">ID</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Name</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Role</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Balance</th>
                            <th class="border border-gray-300 px-4 py-2 text-left">Cards</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @foreach ($users as $user)


                        <tr class="border border-sky-300">
                            <td class="px-4 py-2">{{$user->id}}</td>
                            <td class="px-4 py-2">{{$user->name}}</td>
                            <td class="px-4 py-2">{{$user->role}}</td>
                            <td class="px-4 py-2 flex">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 7.5.415-.207a.75.75 0 0 1 1.085.67V10.5m0 0h6m-6 0h-1.5m1.5 0v5.438c0 .354.161.697.473.865a3.751 3.751 0 0 0 5.452-2.553c.083-.409-.263-.75-.68-.75h-.745M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                  </svg>

                                {{$user->balance}}
                            </td>
                            <td class="px-4 py-2">
                                <span>Silver = {{$user->silver_cards}},</span>
                                <span>Gold = {{$user->gold_cards}},</span>
                                <span>Platinum = {{$user->platinum_cards}}</span>
                            </td>
                        </tr>

                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <script>
            document.getElementById('search').addEventListener('input', function () {
                let filter = this.value.toLowerCase();
                let rows = document.querySelectorAll("#tableBody tr");

                rows.forEach(row => {
                    let text = row.textContent.toLowerCase();
                    row.style.display = text.includes(filter) ? '' : 'none';
                });
            });
        </script>
    </div>
</x-filament-panels::page>
