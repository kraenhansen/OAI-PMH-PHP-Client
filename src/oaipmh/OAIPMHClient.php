<?php
namespace oaipmh;
use \RuntimeException, \SimpleXMLElement;
class OAIPMHClient {
	protected $_curlHandle;
	protected $_baseURL;
	
	public function __construct($baseURL) {
		$this->_curlHandle = curl_init($baseURL);
		curl_setopt($this->_curlHandle, CURLOPT_RETURNTRANSFER, true);
		$this->_baseURL = $baseURL;
	}
	
	public function __destruct() {
		curl_close($this->_curlHandle);
	}
	
	/**
	 * Performs a POST request to the OAI-PMH service.
	 * @param array[string]string $data Requered arguments.
	 * @param array[string]null|string $extraArguments
	 * @throws RuntimeException If something goes wrong.
	 * @return \SimpleXMLElement
	 */
	protected function request($requiredArguments, $optionalArguments = array()) {
		// Process arguments.
		foreach($requiredArguments as $argument => $value) {
			if($value === null) {
				throw RuntimeException("Cannot complete request to the OAI-PMH, required argument '".$argument."' cannot be null.");
			}
		}
		$data = $requiredArguments;
		if(is_array($optionalArguments)) {
			foreach($optionalArguments as $argument => $value) {
				if($value !== null) {
					$data[$argument] = $value;
				}
			}
		}
		
		// Perform the CURL request.
		$query = http_build_query($data);
		curl_setopt($this->_curlHandle, CURLOPT_POST, true);
		curl_setopt($this->_curlHandle, CURLOPT_POSTFIELDS, $query);
		$response = curl_exec($this->_curlHandle);
		if($response === false) {
			throw new RuntimeException("Unsuccessfull response from OAI-PMH service: "+curl_error($this->_curlHandle));
		} else {
			$xml = simplexml_load_string($response);
			if($xml === false) {
				throw new RuntimeException("The OAI-PMH service returned invalid XML.");
			} else {
				return $xml;
			}
		}
	}
	
	/**
	 * This request is used to retrieve an individual metadata record from a repository. Required arguments specify the identifier of the item from which the record is requested and the format of the metadata that should be included in the record. Depending on the level at which a repository tracks deletions, a header with a "deleted" value for the status attribute may be returned, in case the metadata format specified by the metadataPrefix is no longer available from the repository or from the specified item.
	 * Error and Exception Conditions:
	 *  - badArgument - The request includes illegal arguments or is missing required arguments.
	 *  - cannotDisseminateFormat - The value of the metadataPrefix argument is not supported by the item identified by the value of the identifier argument.
	 *  - idDoesNotExist - The value of the identifier argument is unknown or illegal in this repository.
	 * @see http://www.openarchives.org/OAI/openarchivesprotocol.html#GetRecord
	 * @param string $identifier Identifier a required argument that specifies the unique identifier of the item in the repository from which the record must be disseminated.
	 * @param string $metadataPrefix A required argument that specifies the metadataPrefix of the format that should be included in the metadata part of the returned record. A record should only be returned if the format specified by the metadataPrefix can be disseminated from the item identified by the value of the identifier argument. The metadata formats supported by a repository and for a particular record can be retrieved using the ListMetadataFormats request.
	 * @return \SimpleXMLElement
	 */
	public function GetRecord($identifier, $metadataPrefix) {
		return $this->request(array(
			'verb' => __FUNCTION__
		), get_defined_vars());
	}
	
	/**
	 * This request is used to retrieve information about a repository. Some of the information returned is required as part of the OAI-PMH. Repositories may also employ the Identify verb to return additional descriptive information.
	 * Error and Exception Conditions:
	 *  - badArgument - The request includes illegal arguments.
	 * @see http://www.openarchives.org/OAI/openarchivesprotocol.html#Itentify
	 * @return \SimpleXMLElement
	 */
	public function Identify() {
		return $this->request(array(
			'verb' => __FUNCTION__
		), get_defined_vars());
	}
	
	/**
	 * This request is an abbreviated form of ListRecords, retrieving only headers rather than records. Optional arguments permit selective harvesting of headers based on set membership and/or datestamp. Depending on the repository's support for deletions, a returned header may have a status attribute of "deleted" if a record matching the arguments specified in the request has been deleted.
	 * Error and Exception Conditions:
	 *  - badArgument - The request includes illegal arguments or is missing required arguments.
	 *  - badResumptionToken - The value of the resumptionToken argument is invalid or expired.
	 *  - cannotDisseminateFormat - The value of the metadataPrefix argument is not supported by the repository.
	 *  - noRecordsMatch- The combination of the values of the from, until, and set arguments results in an empty list.
	 *  - noSetHierarchy - The repository does not support sets.
	 * @see http://www.openarchives.org/OAI/openarchivesprotocol.html#ListIdentifiers
	 * @param string $metadataPrefix A required argument, which specifies that headers should be returned only if the metadata format matching the supplied metadataPrefix is available or, depending on the repository's support for deletions, has been deleted. The metadata formats supported by a repository and for a particular item can be retrieved using the ListMetadataFormats request.
	 * @param null|string $resumptionToken An exclusive argument with a value that is the flow control token returned by a previous ListIdentifiers request that issued an incomplete list.
	 * @param null|string $from An optional argument with a UTCdatetime value, which specifies a lower bound for datestamp-based selective harvesting.
	 * @param null|string $until An optional argument with a UTCdatetime value, which specifies a upper bound for datestamp-based selective harvesting.
	 * @param null|string $set An optional argument with a setSpec value , which specifies set criteria for selective harvesting.
	 * @return \SimpleXMLElement
	 */
	public function ListIdentifiers($metadataPrefix, $resumptionToken = null, $from = null, $until = null, $set = null) {
		return $this->request(array(
			'verb' => __FUNCTION__
		), get_defined_vars());
	}
	
	/**
	 * This request is used to retrieve the metadata formats available from a repository. An optional argument restricts the request to the formats available for a specific item.
	 * Error and Exception Conditions:
	 *  - badArgument - The request includes illegal arguments or is missing required arguments.
	 *  - idDoesNotExist - The value of the identifier argument is unknown or illegal in this repository.
	 *  - noMetadataFormats - There are no metadata formats available for the specified item.
	 * @see http://www.openarchives.org/OAI/openarchivesprotocol.html#ListMetadataFormats
	 * @param null|string $identifier An optional argument that specifies the unique identifier of the item for which available metadata formats are being requested. If this argument is omitted, then the response includes all metadata formats supported by this repository. Note that the fact that a metadata format is supported by a repository does not mean that it can be disseminated from all items in the repository.
	 * @return \SimpleXMLElement
	 */
	public function ListMetadataFormats($identifier = null) {
		return $this->request(array(
			'verb' => __FUNCTION__
		), get_defined_vars());
	}
	
	/**
	 * This request is an abbreviated form of ListRecords, retrieving only headers rather than records. Optional arguments permit selective harvesting of headers based on set membership and/or datestamp. Depending on the repository's support for deletions, a returned header may have a status attribute of "deleted" if a record matching the arguments specified in the request has been deleted.
	 * Error and Exception Conditions
	 *  - badArgument - The request includes illegal arguments or is missing required arguments.
	 *  - badResumptionToken - The value of the resumptionToken argument is invalid or expired.
	 *  - cannotDisseminateFormat - The value of the metadataPrefix argument is not supported by the repository.
	 *  - noRecordsMatch- The combination of the values of the from, until, and set arguments results in an empty list.
	 *  - noSetHierarchy - The repository does not support sets.
	 * @see http://www.openarchives.org/OAI/openarchivesprotocol.html#ListRecords
	 * @param string $metadataPrefix
	 * @param null|string $resumptionToken
	 * @param null|string $from
	 * @param null|string $until
	 * @param null|string $set
	 * @return \SimpleXMLElement
	 */
	public function ListRecords($metadataPrefix, $resumptionToken = null, $from = null, $until = null, $set = null) {
		return $this->request(array(
			'verb' => __FUNCTION__
		), get_defined_vars());
	}
	
	/**
	 * This request is used to retrieve the set structure of a repository, useful for selective harvesting.
	 * Error and Exception Conditions
	 *  - badArgument - The request includes illegal arguments or is missing required arguments.
	 *  - badResumptionToken - The value of the resumptionToken argument is invalid or expired.
	 *  - noSetHierarchy - The repository does not support sets.
	 * @see http://www.openarchives.org/OAI/openarchivesprotocol.html#ListSets
	 * @param null|string $resumptionToken An exclusive argument with a value that is the flow control token returned by a previous ListSets request that issued an incomplete list.
	 * @return \SimpleXMLElement
	 */
	public function ListSets($resumptionToken = null) {
		return $this->request(array(
			'verb' => __FUNCTION__
		), get_defined_vars());
	}
	
}