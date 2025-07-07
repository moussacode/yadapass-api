{{-- view-carte-etudiante.blade.php --}}
<x-filament::page>
    <div class="flex justify-center">
        {{-- Tu peux réutiliser le composant de carte ici --}}
        @include('carte.print', ['carte' => $record, 'carteInfos' => $record->getCarteInfos()])
    </div>

    <div class="mt-4 flex justify-center space-x-4">
        <a href="{{ route('carte.print', $record) }}" target="_blank" class="filament-button bg-green-600 hover:bg-green-700 text-white">
            Télécharger PDF
        </a>
        <a href="{{ route('carte.preview', $record) }}" target="_blank" class="filament-button bg-blue-600 hover:bg-blue-700 text-white">
            Aperçu externe
        </a>
    </div>
</x-filament::page>
