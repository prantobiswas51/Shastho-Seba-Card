<x-filament-panels::page>
    <style>
        .fi-header-heading {
            display: none;
        }
    </style>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <h1 style="font-size: 41px;text-transform: uppercase;letter-spacing: 0.5px;word-spacing: 6.6px;color: #36d305;font-family: math;"
        class="">Cards and Balance Report</h1>
    <div class="">
        <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow">

            <form method="GET" action="{{ url()->current() }}" class="mb-4 items-center flex gap-2">
                <input type="text" name="search" style="height:40px !important;" placeholder="Search by name or ID..." value="{{ request('search') }}"
                    class="w-full border rounded-lg focus:ring-2 focus:ring-blue-500">
                <div class="flex gap-2 items-center">

                    <button type="submit" class="px-4 py-2 border bg-sky-600 rounded-lg">
                        Search
                    </button>

                    <a href="{{ url()->current() }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                        Clear
                    </a>

                </div>
            </form>

            <div class="overflow-x-auto">


                <table class="w-full mt-3 border border-collapse border-gray-300 table-auto"
                    style="margin-bottom: 20px;">
                    <thead style="background: rebeccapurple;color: #fff;">
                        <tr>
                            <th class="px-4 py-2 border border-gray-300">Id</th>
                            <th class="px-4 py-2 border border-gray-300">Name</th>
                            <th class="px-4 py-2 border border-gray-300">Role</th>
                            <th class="px-4 py-2 border border-gray-300">Balance</th>
                            <th class="px-4 py-2 border border-gray-300">Card</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="px-4 py-2 border border-gray-300">{{$user->id}}</td>
                            <td class="px-4 py-2 border border-gray-300">{{$user->name}}</td>
                            <td class="px-4 py-2 border border-gray-300">{{$user->role}}</td>
                            <td class="px-4 py-2 border border-gray-300 flex">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m8.25 7.5.415-.207a.75.75 0 0 1 1.085.67V10.5m0 0h6m-6 0h-1.5m1.5 0v5.438c0 .354.161.697.473.865a3.751 3.751 0 0 0 5.452-2.553c.083-.409-.263-.75-.68-.75h-.745M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                {{$user->balance}}
                            </td>
                            <td class="px-4 py-2 border border-gray-300">
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
