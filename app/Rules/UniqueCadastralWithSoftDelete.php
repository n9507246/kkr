<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\KadastrovieObekti;

class UniqueCadastralWithSoftDelete implements ValidationRule
{
    protected $id_porucheniya_urr;
    protected $ignoreId;

    public function __construct($id_porucheniya_urr, $ignoreId = null)
    {
        $this->id_porucheniya_urr = $id_porucheniya_urr;
        $this->ignoreId = $ignoreId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = KadastrovieObekti::query()
            ->where('id_porucheniya_urr', $this->id_porucheniya_urr)
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
