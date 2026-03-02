<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\KadastrovieObekti;

class UniqueCadastralWithSoftDelete implements ValidationRule
{
    protected $poruchenieId;
    protected $ignoreId;

    public function __construct($poruchenieId, $ignoreId = null)
    {
        $this->poruchenieId = $poruchenieId;
        $this->ignoreId = $ignoreId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = KadastrovieObekti::query()
            ->where('poruchenie_id', $this->poruchenieId)
            ->where('kadastroviy_nomer', $value);

        // Если нужно игнорировать текущую запись (при обновлении)
        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }

        if ($query->exists()) {
            $fail('Объект с таким кадастровым номером уже существует в этом поручении');
        }
    }
}
