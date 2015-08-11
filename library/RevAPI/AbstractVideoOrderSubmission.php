<?php

namespace RevAPI;

use RevAPI\Exception\UnexpectedValueException;

abstract class AbstractVideoOrderSubmission extends AbstractOrderSubmission
{
    /**
     * Add an input
     *
     * @param AbstractInput $input
     * @throws UnexpectedValueException
     */
    function addInput(AbstractInput $input)
    {
        if (!$input instanceof VideoInput) {
            throw new UnexpectedValueException('The input must be a VideoInput');
        }

        /**
         * @var $input VideoInput
         */

        $data = array(
            'uri' => $input->getURI()
        );

        if (null !== $input->getVideoLength()) {
            $data['video_length_seconds'] = $input->getVideoLength();
        }

        $this->inputs[] = $data;
    }
}