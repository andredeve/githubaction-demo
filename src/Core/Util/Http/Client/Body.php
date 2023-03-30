<?php /** @noinspection PhpUnused */

namespace Core\Util\Http\Client;

class Body {
    /**
     * @var string
     */
    private $content;

    public function __construct($result) {
        $this->content = $result;
    }

    public function __toString()
    {
        return $this->content;
    }

    public function toArray(): ?array {
        return json_decode($this->content, true);
    }

    /**
     * @return object
     */
    public function toObject() {
        return json_decode($this->content);
    }

    /**
     * @return float|int|string
     */
    public function toScalar() {
        if (is_int($this->content)) {
            return $this->content;
        } else if (is_double($this->content)) {
            return $this->content;
        } else {
            return $this->content;
        }
    }
}