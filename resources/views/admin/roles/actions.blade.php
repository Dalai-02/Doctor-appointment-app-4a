<div class="flex items-center space-x-2">
    <x-wire-button href="{{route('admin.roles.edit', $role)}}" blue xs>
        <i class="fa- solid fa-pen-to-square"></i>

    </x-wire-button>

    <form actioin="{{ route('admin.role.destroy', $role)}}" method="POST" class="inline">
        @csrf
        @method('DELETE')
        <x-wire-button type="submit" red xs>
            <i class="fa-solid fa-trash"></i>
    </form>
</div>