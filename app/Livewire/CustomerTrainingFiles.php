<?php

namespace App\Livewire;

use App\Models\TrainerFile;
use Livewire\Component;

class CustomerTrainingFiles extends Component
{
    public string $activeTab = 'TRAINING_DECK';

    public array $tabs = [
        'TRAINING_DECK' => ['label' => 'Training Deck', 'icon' => 'fas fa-chalkboard-teacher'],
        'TRAINING_SOP' => ['label' => 'Training SOP', 'icon' => 'fas fa-clipboard-list'],
        'TRAINING_GUIDELINE' => ['label' => 'Training Guideline', 'icon' => 'fas fa-book-open'],
        'TRAINING_RECORDING' => ['label' => 'Training Recording', 'icon' => 'fas fa-video'],
    ];

    public function switchTab(string $tab): void
    {
        if (array_key_exists($tab, $this->tabs)) {
            $this->activeTab = $tab;
        }
    }

    public function getFilesProperty()
    {
        $title = str_replace('_', ' ', $this->activeTab);

        return TrainerFile::where('title', $title)
            ->orderBy('module_type')
            ->orderBy('version')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('module_type');
    }

    public function downloadFile(int $fileId)
    {
        $file = TrainerFile::findOrFail($fileId);

        if ($file->is_link) {
            return redirect($file->file_path);
        }

        return redirect()->route('customer.training-file.download', $file);
    }

    public function render()
    {
        return view('livewire.customer-training-files');
    }
}
