<?php

namespace Gloord\DeltaParser\Parser;


trait HelperFunctions
{

    /**
     * Split text with spliton outside of the defined container marks
     *
     * @param string $text
     * @param string $splitToken
     * @param array $containerMarks
     * @return array
     */
    public function splitSpecial(string $text, string $splitToken, array $containerMarks = [['<', '>']])
    {
        //get all potential matches
        preg_match_all('/' . preg_quote($splitToken, '/') . '/', $text, $potentialSplitPoints, PREG_OFFSET_CAPTURE);

        $result = [];
        //there are no split tokens found in the string
        if (empty($potentialSplitPoints[0])) {
            $result[] = $text;
            return $result;
        }

        $chunkMap = [];

        foreach ($containerMarks as $delimiter) {
            $chunkMap = array_merge($chunkMap, $this->mapChunks($text, $delimiter));
        }

        //get only level 0
        $chunkMap = array_filter($chunkMap, function ($item) {
            if ($item['level'] === 0) {
                return $item;
            }
        });

        //filter out split tokens within range
        $evaluatedSplitPoints = array_filter($potentialSplitPoints[0], function ($item) use ($chunkMap) {
            foreach ($chunkMap as $point) {
                //is within container
                if ($point['start'] < $item[1] && $item[1] < $point['end']) {
                    return false;
                }
            }
            return $item;
        });

        $currPos = 0;
        foreach ($evaluatedSplitPoints as $point) {
            $result[] = substr($text, $currPos, $point[1] - $currPos);
            $currPos += $point[1] - $currPos + strlen($point[0]);
        }

        $result[] = substr($text, $currPos);
        return $result;
    }

    /**
     * Return start and end position of opener and closer including the corresponding level (starting from index 0)
     *
     * @param string $text
     * @param array $containerMarks
     * @return array | bool
     */
    public function mapChunks(string $text, $containerMarks = ['<', '>'])
    {
        $chunkMap = [];

        if ($containerMarks[0] === $containerMarks[1]
            || empty($containerMarks[0]) || empty($containerMarks[1])) {
            return $chunkMap;
        }

        preg_match_all('/' . preg_quote($containerMarks[0], '/') . '/', $text, $openPoints, PREG_OFFSET_CAPTURE);
        preg_match_all('/' . preg_quote($containerMarks[1], '/') . '/', $text, $closePoints, PREG_OFFSET_CAPTURE);

        if (count($openPoints[0]) !== count($closePoints[0])
            || empty($openPoints[0]) || empty($closePoints[0])) {

            return $chunkMap;
        }

        $chunkStarts = [];

        while (!empty($openPoints[0]) || !empty($closePoints[0])) {
            if (!empty($openPoints[0]) && $openPoints[0][0][1] < $closePoints[0][0][1]) {
                $chunkStarts[] = $openPoints[0][0][1];
                //remove first element
                array_shift($openPoints[0]);
            } else {
                $chunkEnd = $closePoints[0][0][1];
                //remove first element
                array_shift($closePoints[0]);
                if (!empty($chunkStarts)) {
                    $chunkMap[] = [
                        'level' => count($chunkStarts) - 1,
                        'start' => array_pop($chunkStarts),
                        'end' => $chunkEnd,
                    ];
                }
            }
        }
        return $chunkMap;
    }
}