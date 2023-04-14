@extends('dpanel.layouts.app')

@section('title', 'Manage Access')

@push('css')
    <link rel="stylesheet" href="{{ asset('dd4you/dpanel/js/animated-tags-input/style.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('dd4you/dpanel/js/animated-tags-input/tags-input.js') }}"></script>
    <script>
        const editRole = (id, roleName, permissions) => {
            document.getElementById('edit_role_form').action = `${window.location.origin}/dpanel/manage-access/${id}`;
            document.getElementById('role_name').value = roleName;
            let arr = [];
            JSON.parse(permissions).forEach(permission => {
                arr.push(permission.name)
            });
            selectTags('role_edit_permissions', arr);
            showBottomSheet('EditRoleBottomSheet');
        }
        const editUser = (id, first_name, last_name, email, roles) => {
            console.log(last_name);
            document.getElementById('edit_user_form').action = `${window.location.origin}/dpanel/manage-access/${id}`;
            document.getElementById('first_name').value = first_name;
            document.getElementById('last_name').value = last_name;
            document.getElementById('email').value = email;
            let arr = [];
            JSON.parse(roles).forEach(role => {
                arr.push(role.name)
            });
            selectTags('user_edit_roles', arr);
            showBottomSheet('EditUserBottomSheet');
        }
    </script>
@endpush

@section('body_content')
    {{-- Roles --}}
    <div class="bg-white rounded mb-1 shadow flex justify-between items-center">
        <p class="font-medium px-2">Manage Role's Permissions</p>
        <button onclick="showBottomSheet('RoleBottomSheet')" class="bg-gray-800 px-2 py-1 rounded-r text-white">New
            Role</button>
    </div>

    <x-dpanel::table>
        <x-slot:thead>
            <tr>
                <th scope="col" class="pl-3 py-2 text-left w-12 ">
                    #
                </th>

                <th scope="col" class="pl-3 py-2 text-left font-medium tracking-wider">
                    Role
                </th>

                <th scope="col" class="pl-3 py-2 text-left font-medium tracking-wider">
                    Has Permissions
                </th>
                <th scope="col" class="pl-3 py-2 text-left font-medium tracking-wider">
                    Create
                </th>
                <th scope="col" class="pl-3 py-2 text-left font-medium tracking-wider">
                    Update
                </th>

                <th scope="col" class="pl-3 py-2 text-center font-medium tracking-wider">
                    Action
                </th>
            </tr>
        </x-slot:thead>

        <x-slot:tbody>
            @foreach ($roles as $item)
                <tr>
                    <td class="pl-3 py-2">
                        {{ $loop->iteration }}
                    </td>
                    <td class="pl-3 py-2 capitalize whitespace-nowrap">{{ $item->name }}</td>
                    <td class="pl-3 py-2">
                        @if ($item->name == 'super-admin')
                            No Permission Needed
                        @else
                            <div class="flex flex-wrap gap-1">
                                @foreach ($item->permissions as $subitem)
                                    <span style="background: {{ Arr::random($bgColorsForWhiteText, 1)[0] }}"
                                        class="rounded-full px-2 text-white capitalize text-xs shadow pb-0.5 whitespace-nowrap">{{ $subitem->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">{{ $item->created_at->format('d-m-Y') }}</td>
                    <td class="px-4 py-2 whitespace-nowrap">{{ $item->updated_at->format('d-m-Y') }}</td>
                    <td class="px-4 py-2 flex justify-center">
                        @if ($item->name == 'super-admin')
                            <span
                                class="ml-1 text-red-500 cursor-not-allowed bg-red-100 focus:outline-none border border-red-500 rounded-full w-6 h-6 flex justify-center items-center">
                                <i class='bx bx-block'></i>
                            </span>
                        @else
                            <button
                                onclick="editRole('{{ $item->id }}', '{{ $item->name }}', '{{ $item->permissions }}')"
                                class="ml-1 text-blue-500 bg-blue-100 focus:outline-none border border-blue-500 rounded-full w-6 h-6 flex justify-center items-center">
                                <i class='bx bx-edit'></i>
                            </button>
                        @endif

                    </td>
                </tr>
            @endforeach

        </x-slot:tbody>

        <x-slot:pagination>
            {{ $roles->links('dpanel.layouts.pagination') }}
        </x-slot:pagination>
    </x-dpanel::table>

    {{-- Roles End --}}

    <hr class="mt-4">
    <hr>
    <hr>
    <hr>
    <hr class="mb-4">

    {{-- User's Roles --}}
    <div class="bg-white rounded mb-1 shadow flex justify-between items-center">
        <p class="font-medium px-2 py-1">Manage User's Roles</p>
        <button onclick="showBottomSheet('UserBottomSheet')" class="bg-gray-800 px-2 py-1 rounded-r text-white">New
            User</button>
    </div>
    <x-dpanel::table>
        <x-slot:thead>
            <tr>
                <th scope="col" class="pl-3 py-2 text-left w-12 ">
                    #
                </th>

                <th scope="col" class="pl-3 py-2 text-left font-medium tracking-wider">
                    Name
                </th>

                <th scope="col" class="pl-3 py-2 text-left font-medium tracking-wider">
                    Email
                </th>

                <th scope="col" class="pl-3 py-2 text-left font-medium tracking-wider">
                    Has Role
                </th>

                <th scope="col" class="pl-3 py-2 text-left font-medium tracking-wider">
                    Create
                </th>
                <th scope="col" class="pl-3 py-2 text-left font-medium tracking-wider">
                    Update
                </th>

                <th scope="col" class="pl-3 py-2 text-center font-medium tracking-wider">
                    Action
                </th>
            </tr>
        </x-slot:thead>

        <x-slot:tbody>
            @foreach ($users as $item)
                <tr>
                    <td class="pl-3 py-2">
                        {{ $loop->iteration }}
                    </td>
                    <td class="pl-3 py-2 capitalize whitespace-nowrap">{{ $item->full_name }}</td>

                    <td class="pl-3 py-2 capitalize whitespace-nowrap">{{ $item->email }}</td>
                    <td class="pl-3 py-2">
                        <div class="flex flex-wrap gap-1">
                            @forelse ($item->roles as $subitem)
                                <span style="background: {{ Arr::random($bgColorsForWhiteText, 1)[0] }}"
                                    class="rounded-full px-2 text-white capitalize text-xs shadow pb-0.5 whitespace-nowrap">{{ $subitem->name }}</span>
                            @empty
                                Normal User | No Role Assign
                            @endforelse
                        </div>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">{{ $item->created_at->format('d-m-Y') }}</td>
                    <td class="px-4 py-2 whitespace-nowrap">{{ $item->updated_at->format('d-m-Y') }}</td>
                    <td class="px-4 py-2 flex justify-center">
                        @if ($item->roles->contains('name', 'super-admin'))
                            <span
                                class="ml-1 text-red-500 cursor-not-allowed bg-red-100 focus:outline-none border border-red-500 rounded-full w-6 h-6 flex justify-center items-center">
                                <i class='bx bx-block'></i>
                            </span>
                        @else
                            <button
                                onclick="editUser('{{ $item->id }}','{{ $item->first_name }}','{{ $item->last_name }}','{{ $item->email }}','{{ $item->roles->setHidden(['pivot']) }}')"
                                class="ml-1 text-blue-500 bg-blue-100 focus:outline-none border border-blue-500 rounded-full w-6 h-6 flex justify-center items-center">
                                <i class='bx bx-edit'></i>
                            </button>
                        @endif

                    </td>
                </tr>
            @endforeach

        </x-slot:tbody>

        <x-slot:pagination>
            {{ $roles->links('dpanel.layouts.pagination') }}
        </x-slot:pagination>
    </x-dpanel::table>

    {{-- User's Roles End --}}

    {{-- Role Bottom Sheets --}}
    <x-dpanel::modal.bottom-sheet sheetId="RoleBottomSheet" title="New Role">
        <div class="flex justify-center items-center min-h-[30vh] md:min-h-[50vh]">
            <form action="{{ route('dpanel.manage-access.store') }}" method="post" class="grid grid-cols-1 gap-y-4">
                @csrf
                <input type="hidden" name="type" value="role" required>
                <input type="text" placeholder="Enter Role eg Demo User" name="role_name" required pattern="[-a-zA-Z\s]+"
                    class="w-full p-2 rounded bg-white shadow-lg shadow-gray-200 focus-within:outline-none ">

                <select name="permissions[]" multiple data-placeholder="Select Permissions">
                    @foreach ($permissions as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>

                <button
                    class="bg-gray-800 w-full rounded py-1 text-white shadow-lg shadow-gray-200 font-medium uppercase text-lg">Submit</button>
            </form>
        </div>
    </x-dpanel::modal.bottom-sheet>

    <x-dpanel::modal.bottom-sheet sheetId="EditRoleBottomSheet" title="Edit Role">
        <div class="flex justify-center items-center min-h-[30vh] md:min-h-[50vh]">
            <form id="edit_role_form" action="" method="post" class="grid grid-cols-1 gap-y-4">
                @csrf
                @method('put')
                <input type="hidden" name="type" value="role" required>
                <input type="text" placeholder="Enter Role eg Demo User" id="role_name" name="role_name" required
                    pattern="[-a-zA-Z\s]+"
                    class="w-full p-2 rounded bg-white shadow-lg shadow-gray-200 focus-within:outline-none ">

                <select id="role_edit_permissions" name="permissions[]" multiple data-placeholder="Select Permissions">
                    @foreach ($permissions as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>

                <button
                    class="bg-gray-800 w-full rounded py-1 text-white shadow-lg shadow-gray-200 font-medium uppercase text-lg">Update</button>
            </form>
        </div>
    </x-dpanel::modal.bottom-sheet>
    {{-- Role Bottom Sheets End --}}


    {{-- User Bottom Sheets --}}
    <x-dpanel::modal.bottom-sheet sheetId="UserBottomSheet" title="New User">
        <div class="flex justify-center items-center min-h-[30vh] md:min-h-[50vh]">
            <form action="{{ route('dpanel.manage-access.store') }}" method="post" class="grid grid-cols-1 gap-y-4">
                @csrf
                <input type="hidden" name="type" value="user" required>
                <input type="text" placeholder="Enter First Name" name="first_name" required
                    class="w-full p-2 rounded bg-white shadow-lg shadow-gray-200 focus-within:outline-none ">
                <input type="text" placeholder="Enter Last Name" name="last_name" required
                    class="w-full p-2 rounded bg-white shadow-lg shadow-gray-200 focus-within:outline-none ">
                <input type="email" placeholder="Enter Email" name="email" required
                    class="w-full p-2 rounded bg-white shadow-lg shadow-gray-200 focus-within:outline-none ">
                <input type="password" placeholder="Enter Password" name="password" required
                    class="w-full p-2 rounded bg-white shadow-lg shadow-gray-200 focus-within:outline-none ">
                <select id="user_new_roles" name="roles[]" multiple data-placeholder="Select Roles">
                    @foreach ($roles as $item)
                        @if ($item->name == 'super-admin')
                            @continue
                        @endif
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>
                <button
                    class="bg-gray-800 w-full rounded py-1 text-white shadow-lg shadow-gray-200 font-medium uppercase text-lg">Submit</button>
            </form>
        </div>
    </x-dpanel::modal.bottom-sheet>

    <x-dpanel::modal.bottom-sheet sheetId="EditUserBottomSheet" title="User Role">
        <div class="flex justify-center items-center min-h-[30vh] md:min-h-[50vh]">
            <form id="edit_user_form" action="" method="post" class="grid grid-cols-1 gap-y-4">
                @csrf
                @method('put')
                <input type="hidden" name="type" value="user" required>
                <input type="text" placeholder="Enter First Name" id="first_name" name="first_name" required
                    class="w-full p-2 rounded bg-white shadow-lg shadow-gray-200 focus-within:outline-none ">
                <input type="text" placeholder="Enter Last Name" id="last_name" name="last_name" required
                    class="w-full p-2 rounded bg-white shadow-lg shadow-gray-200 focus-within:outline-none ">
                <input type="email" placeholder="Enter Email" id="email" name="email" required
                    class="w-full p-2 rounded bg-white shadow-lg shadow-gray-200 focus-within:outline-none ">
                <input type="password" placeholder="Enter Password" name="password"
                    class="w-full p-2 rounded bg-white shadow-lg shadow-gray-200 focus-within:outline-none ">
                <select id="user_edit_roles" name="roles[]" multiple data-placeholder="Select Roles">
                    @foreach ($roles as $item)
                        @if ($item->name == 'super-admin')
                            @continue
                        @endif
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>
                <button
                    class="bg-gray-800 w-full rounded py-1 text-white shadow-lg shadow-gray-200 font-medium uppercase text-lg">Update</button>
            </form>
        </div>
    </x-dpanel::modal.bottom-sheet>
    {{-- User Bottom Sheets End --}}

    <x-dpanel::modal.bottom-sheet-js hideOnClickOutside="true" />
@endsection
