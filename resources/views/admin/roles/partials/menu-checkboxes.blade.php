{{-- Recursive checkbox list mirroring the menu tree. --}}

@foreach ($nodes as $node)
    <label class="flex items-center gap-2 rounded px-1 py-1 text-sm hover:bg-slate-50 dark:hover:bg-slate-800"
           style="padding-left: {{ 0.25 + $depth * 1.25 }}rem">
        <input type="checkbox" name="menus[]" value="{{ $node->id }}"
               @checked(in_array($node->id, old('menus', $assigned), true))
               class="rounded border-slate-300 text-brand-600">

        <x-icon :name="$node->icon" class="size-3.5 shrink-0 text-slate-400" />
        <span class="truncate">{{ $node->title }}</span>

        @if ($node->permission_name)
            <code class="ml-auto shrink-0 text-[10px] text-amber-600 dark:text-amber-400">{{ $node->permission_name }}</code>
        @endif
    </label>

    @if ($node->children->isNotEmpty())
        @include('admin.roles.partials.menu-checkboxes', [
            'nodes' => $node->children,
            'depth' => $depth + 1,
            'assigned' => $assigned,
        ])
    @endif
@endforeach
