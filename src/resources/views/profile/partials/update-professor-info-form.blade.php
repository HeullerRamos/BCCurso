<section>
    <header>
        <h2 class="text-lg font-medium text-black-900 dark:text-black-100">
            {{ __('Informações do Professor') }}
        </h2>

        <p class="mt-1 text-sm text-black-600 dark:text-black-400">
            {{ __("Atualize os seus dados de professor.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="titulacao" :value="__('Titulação')" style="color:black;"/>
            <x-text-input id="titulacao" name="titulacao" type="text" class="mt-1 block w-full input-field" :value="old('titulacao', $user->titulacao)" autofocus autocomplete="titulacao" />
            <x-input-error class="mt-2" :messages="$errors->get('titulacao')" />
        </div>

        <div>
            <x-input-label for="biografia" :value="__('Biografia')" style="color:black;"/>
            <x-text-input id="biografia" name="biografia" type="text" class="mt-1 block w-full input-field" :value="old('biografia', $user->biografia)" autofocus autocomplete="biografia" />
            <x-input-error class="mt-2" :messages="$errors->get('biografia')" />
        </div>

        <div>
            <x-input-label for="area" :value="__('Área')" style="color:black;"/>
            <x-text-input id="area" name="area" type="text" class="mt-1 block w-full input-field" :value="old('area', $user->area)" autofocus autocomplete="area" />
            <x-input-error class="mt-2" :messages="$errors->get('area')" />
        </div>

        <!-- Links -->
        <div class="mt-4">
        <x-input-label for="links" :value="__('Links')" style="color:black;"/>
            <div id="links-container">
                @if($professor && $professor->curriculos->first())
                    @forelse ($professor->curriculos->first()->links as $link)
                        <div class="link-input-group mb-2 flex items-center">
                            <x-text-input class="block w-full" type="text" name="links[{{ $link->id }}]" value="{{ $link->link }}" />
                            <button type="button" class="delete-link-btn ml-2 text-red-500 font-bold" data-id="{{ $link->id }}">X</button>
                        </div>
                    @empty
                    @endforelse
                @endif
            </div>
            <div id="new-links-container">  
                @foreach (old('new_links', []) as $index => $oldLinkUrl)
                <div class="link-input-group-new mb-2 flex items-center">
                    <x-text-input class="block w-full" name="new_links[]" :value="$oldLinkUrl" />
                    <button type="button" class="remove-new-link-btn ml-2 text-red-500 font-bold">X</button>
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('new_links.' . $index)" />
                @endforeach
            </div>
            <button type="button" id="add-link-btn" class="add-link mt-2" style="color:black;">+</button>
        </div>

        <div class="mt-4">
            <x-input-label for="foto" :value="__('Foto')" style="color:black;"/>
            @if (count($user->fotos) > 0)
                @foreach ($user->fotos as $ft)
                    <button class="btn text-danger" type="submit" form="deletar-fotos{{ $ft->id }}">X</button>
                    <img src="{{ URL::asset('storage') }}/{{ $ft->foto }}" class="img-responsive"
                        style="max-height:100px; max-width:100px;">
                @endforeach
            @endif
            <input type="file" name="fotos[]" id="fotos" class="form-control" multiple>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button style="background-color:black;" >{{ __('Salvar') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-black-600 dark:text-black-400"
                >{{ __('Salvo.') }}</p>
            @endif
        </div>
    </form>
    
    @if (count($user->fotos) > 0)
        @foreach ($user->fotos as $ft)
            <form id="deletar-fotos{{ $ft->id }}"
                action="{{ route('profile.delete_foto', ['id' => $ft->id]) }}" method="post">
                @csrf
                @method('delete')
            </form>
        @endforeach
    @endif
</section>

<!-- script para adicionar links usando o botão +,excluir usando x , alterar mudando o link e também apagar apagando o link do campo-->
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('add-link-btn').addEventListener('click', function () {
        const container = document.getElementById('new-links-container');
        const newLinkGroupHTML = `
            <div class="link-input-group-new mb-2 flex items-center">
                <input class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full" type="text" name="new_links[]" placeholder="https://novo-link.com">
                <button type="button" class="remove-new-link-btn ml-2 text-red-500 font-bold">X</button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newLinkGroupHTML);
    });
    document.querySelector('body').addEventListener('click', async function(event) {
        if (event.target.classList.contains('delete-link-btn')) {
            const button = event.target;
            const linkId = button.dataset.id;
            
            if (!confirm('Tem certeza que deseja excluir este link permanentemente?')) return;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                const response = await fetch(`/links/${linkId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    button.closest('.link-input-group').remove();
                } else {
                    alert('Erro ao excluir: ' + (data.message || 'Tente novamente.'));
                }
            } catch (error) {
                console.error('Erro:', error);
                alert('Ocorreu um erro de comunicação.');
            }
        }

        if (event.target.classList.contains('remove-new-link-btn')) {
            event.target.closest('.link-input-group-new').remove();
        }
    });
});
</script>
